const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const mysql = require('mysql2/promise');
const moment = require('moment');

const app = express();
const server = http.createServer(app);
const io = new Server(server, {
    cors: {
        origin: '*',
        methods: ['GET', 'POST']
    }
});

const pool = mysql.createPool({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'khsx',
    waitForConnections: true,
    connectionLimit: 20,
    queueLimit: 0
});

let machineStates = {};
async function getOverdueWorkingPlans() {
    const conn = await pool.getConnection();
    const [rows] = await conn.execute(`
        SELECT
            pp.*, ms.machine_id, ms.production_order_id,
            p.name AS product_name, sp.name AS process_name
        FROM production_plans pp
        JOIN machine_schedules ms ON ms.machine_id = pp.machine_id
        LEFT JOIN products p ON pp.product_id = p.id
        LEFT JOIN specs sp ON sp.process_id = pp.process_id AND sp.product_id = pp.product_id
        WHERE pp.status = 'working'
        AND pp.end_time < NOW()
        ORDER BY pp.end_time ASC
    `);
    conn.release();
    return rows;
}

async function getProductProgress(productId) {
    const conn = await pool.getConnection();
    const [rows] = await conn.execute(`
        SELECT SUM(total_quantity) AS total_qty, SUM(quantity_done) AS done_qty
        FROM production_plans
        WHERE product_id = ?
    `, [productId]);
    conn.release();

    const total_qty = rows[0].total_qty || 0;
    const done_qty = rows[0].done_qty || 0;

    return total_qty === 0 ? 0 : done_qty / total_qty;
}

async function emitAllOrderProgress() {
    const conn = await pool.getConnection();
    const [orders] = await conn.execute(`
        SELECT DISTINCT order_id, product_id
        FROM production_plans
        WHERE order_id IS NOT NULL AND product_id IS NOT NULL
    `);
    conn.release();

    for (const { order_id, product_id } of orders) {
        const progress = await getOrderProgress(order_id, product_id);
        io.emit('order-progress', {
            order_id,
            product_id,
            progress
        });
    }
}

async function emitMachineStates() {
    const conn = await pool.getConnection();
    const [runningPlans] = await conn.execute(`
        SELECT * FROM production_plans
        WHERE status = 'working'
    `);
    conn.release();

    for (const plan of runningPlans) {
        const payload = {
            machine_id: plan.machine_id,
            plan_id: plan.id,
            product_id: plan.product_id,
            current_product: plan.product_id,
            process_id: plan.process_id,
            process_name: plan.process_id,
            lot_number: plan.lot_number,
            quantity_total: plan.total_quantity,
            quantity_done: plan.quantity_done,
            status: 'working',
            start_time: plan.start_time,
            end_time: plan.end_time,
            timestamp: new Date().toISOString(),
            progress: plan.total_quantity > 0 ? Math.floor((plan.quantity_done / plan.total_quantity) * 100) : 0
        };

        io.emit('machine-data', payload);
    }
}

async function emitProductProgress() {
    const conn = await pool.getConnection();
    const [products] = await conn.execute(`
        SELECT DISTINCT product_id FROM production_plans
        WHERE product_id IS NOT NULL
    `);

    for (const { product_id } of products) {
        const [rows] = await conn.execute(`
            SELECT SUM(total_quantity) AS total_qty, SUM(quantity_done) AS done_qty
            FROM production_plans
            WHERE product_id = ?
        `, [product_id]);

        const total_qty = rows[0].total_qty || 0;
        const done_qty = rows[0].done_qty || 0;
        const progress = total_qty === 0 ? 0 : done_qty / total_qty;

        io.emit('product-progress', {
            product_id,
            progress
        });
    }

    conn.release();
}

async function getNextPlannedPlans() {
    const conn = await pool.getConnection();

    // Bước 1: chuyển scheduled → planned nếu đến giờ
    await conn.execute(`
        UPDATE production_plans
        SET status = 'planned'
        WHERE status = 'scheduled' AND start_time <= NOW()
    `);

    // Bước 2: lấy các plan planned
    const [rows] = await conn.execute(`
        SELECT
            pp.*, ms.machine_id, ms.production_order_id,
            p.name AS product_name, sp.name AS process_name
        FROM production_plans pp
        JOIN machine_schedules ms ON ms.machine_id = pp.machine_id
        LEFT JOIN products p ON pp.product_id = p.id
        LEFT JOIN specs sp ON sp.process_id = pp.process_id AND sp.product_id = pp.product_id
        WHERE pp.status = 'planned'
        AND pp.start_time <= NOW()
        ORDER BY pp.start_time ASC
    `);

    conn.release();
    return rows;
}



async function updatePlanStatus(planId, status) {
    const conn = await pool.getConnection();
    await conn.execute(`UPDATE production_plans SET status = ? WHERE id = ?`, [status, planId]);
    conn.release();
}

async function updatePlanQuantityDone(planId, quantity) {
    const conn = await pool.getConnection();
    await conn.execute(`UPDATE production_plans SET quantity_done = ? WHERE id = ?`, [quantity, planId]);
    conn.release();
}

async function updateHistory(plan, quantity) {
    const conn = await pool.getConnection();
    const [existing] = await conn.execute(`
        SELECT * FROM production_histories
        WHERE production_order_id = ? AND product_id = ? AND process_id = ?
    `, [plan.production_order_id, plan.product_id, plan.process_id]);

    if (existing.length) {
        await conn.execute(`
            UPDATE production_histories SET completed_quantity = ? WHERE id = ?
        `, [quantity, existing[0].id]);
    } else {
        const id = `HIST${Math.floor(Math.random() * 100000).toString().padStart(5, '0')}`;
        await conn.execute(`
            INSERT INTO production_histories (id, production_order_id, product_id, process_id, completed_quantity, date)
            VALUES (?, ?, ?, ?, ?, NOW())
        `, [id, plan.production_order_id, plan.product_id, plan.process_id, quantity]);
    }

    conn.release();
}

async function updateInventoryOnPlanFinish(plan) {
    const conn = await pool.getConnection();

    const [bomRows] = await conn.execute(`
        SELECT * FROM bom_items
        WHERE product_id = ? AND process_id = ?
    `, [plan.product_id, plan.process_id]);

    const [unitRow] = await conn.execute(`
        SELECT u.name FROM order_details od
        JOIN units u ON od.unit_id = u.id
        WHERE od.order_id = ? AND od.product_id = ?
        LIMIT 1
    `, [plan.order_id, plan.product_id]);

    const unitName = unitRow.length > 0 ? unitRow[0].name : '';
    const convertToTon = unitName === 'Bao' ? 0.05 : 1;
    const totalQtyTon = plan.total_quantity * convertToTon;

    for (const bom of bomRows) {
        const qtyOut = bom.quantity_output / 100 * totalQtyTon;
        const qtyIn = bom.quantity_input / 100 * totalQtyTon;

        if (bom.output_id) {
            await conn.execute(`
                UPDATE inventories
                SET quantity = quantity + ?, last_updated = NOW()
                WHERE item_id = ? AND item_type = ?
            `, [
                qtyOut,
                bom.output_id,
                bom.output_type
            ]);

            console.log(`✅ [Inventory] + ${qtyOut} → ${bom.output_id} (${bom.output_type})`);
        }

        if (bom.input_material_id) {
            await conn.execute(`
                UPDATE inventories
                SET quantity = GREATEST(0, quantity - ?), last_updated = NOW()
                WHERE item_id = ? AND item_type = 'materials'
            `, [
                qtyIn,
                bom.input_material_id
            ]);

            console.log(`✅ [Inventory] - ${qtyIn} từ ${bom.input_material_id}`);
        }
    }

    conn.release();
}

async function markPlanCompleted(plan) {
    const conn = await pool.getConnection();

    await conn.execute(`UPDATE production_plans SET status = 'finished' WHERE id = ?`, [plan.id]);
    await conn.execute(`UPDATE machine_schedules SET status = 'finished' WHERE production_order_id = ? AND machine_id = ?`,
        [plan.production_order_id, plan.machine_id]);

    const orderProgress = await getOrderProgress(plan.order_id, plan.product_id);
    io.emit('order-progress', {
        order_id: plan.order_id,
        product_id: plan.product_id,
        progress: orderProgress
    });

    await emitProductProgress();
    await updateInventoryOnPlanFinish(plan);

    conn.release();
}

async function getOrderProgress(orderId, productId) {
    const conn = await pool.getConnection();

    let allPlansQuery = `SELECT COUNT(*) AS total FROM production_plans WHERE product_id = ?`;
    let completedPlansQuery = `SELECT COUNT(*) AS completed FROM production_plans WHERE product_id = ? AND status = 'finished'`;

    const params = [productId];

    if (orderId !== null) {
        allPlansQuery += ` AND order_id = ?`;
        completedPlansQuery += ` AND order_id = ?`;
        params.push(orderId);
    }

    const [allPlans] = await conn.execute(allPlansQuery, params);
    const [completedPlans] = await conn.execute(completedPlansQuery, params);
    conn.release();

    const total = allPlans[0].total;
    const completed = completedPlans[0].completed;
    return total === 0 ? 0 : completed / total;
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function simulateProduction(plan) {
    const now = moment();
    const planStart = moment(plan.start_time);
    const planEnd = moment(plan.end_time);
    const durationMin = planEnd.diff(planStart, 'minutes');
    const totalQty = plan.total_quantity;

    // Nếu plan đang là 'scheduled' → chuyển sang 'working' ngay khi vào simulate
    if (plan.status === 'scheduled') {
        await updatePlanStatus(plan.id, 'working');
        plan.status = 'working';
    }

    if (now.isBefore(planStart)) {
        const waitMs = planStart.diff(now, 'milliseconds');
        console.log(`⏳ Chờ đến giờ start của Plan ${plan.id} → chờ ${Math.round(waitMs / 1000)} giây...`);
        await sleep(waitMs);
    }

    // Nếu đã quá hạn → force finish 100%
    if (now.isAfter(planEnd)) {
        console.log(`⚠️ Plan ${plan.id} đã quá hạn → force cập nhật finished 100%`);
        await updatePlanQuantityDone(plan.id, totalQty);
        await updatePlanStatus(plan.id, 'finished');
        await updateHistory(plan, totalQty);
        await markPlanCompleted(plan);

        io.emit('machine-data', {
            machine_id: plan.machine_id,
            plan_id: plan.id,
            product_id: plan.product_id,
            current_product: plan.product_name || plan.product_id,
            process_id: plan.process_id,
            process_name: plan.process_name || plan.process_id,
            lot_number: plan.lot_number,
            quantity_total: totalQty,
            quantity_done: totalQty,
            status: 'finished',
            start_time: plan.start_time,
            end_time: plan.end_time,
            timestamp: new Date().toISOString(),
            progress: 100
        });

        const productProgress = await getProductProgress(plan.product_id);
        io.emit('product-progress', {
            product_id: plan.product_id,
            progress: productProgress
        });

        return;
    }

    await updatePlanStatus(plan.id, 'working');

    const intervalMs = (durationMin * 60 * 1000) / totalQty;

    const conn = await pool.getConnection();
    const [row] = await conn.execute(`SELECT quantity_done FROM production_plans WHERE id = ?`, [plan.id]);
    conn.release();

    let produced = row[0].quantity_done || 0;

    for (; produced < totalQty; produced++) {
        const progress = Math.floor(((produced + 1) / totalQty) * 100);

        io.emit('machine-data', {
            machine_id: plan.machine_id,
            plan_id: plan.id,
            product_id: plan.product_id,
            current_product: plan.product_name || plan.product_id,
            process_id: plan.process_id,
            process_name: plan.process_name || plan.process_id,
            lot_number: plan.lot_number,
            quantity_total: totalQty,
            quantity_done: produced + 1,
            status: (produced + 1) === totalQty ? 'finished' : 'working',
            start_time: plan.start_time,
            end_time: plan.end_time,
            timestamp: new Date().toISOString(),
            progress
        });

        await updateHistory(plan, produced + 1);
        await updatePlanQuantityDone(plan.id, produced + 1);

        if ((produced + 1) === totalQty) {
            await updatePlanStatus(plan.id, 'finished');
        }

        const productProgress = await getProductProgress(plan.product_id);
        io.emit('product-progress', {
            product_id: plan.product_id,
            progress: productProgress
        });

        await sleep(intervalMs);
    }
    // Sau vòng for → nếu end_time đã qua mà chưa finished → force finish lại
    const nowAfterLoop = moment();
    if (nowAfterLoop.isAfter(planEnd)) {
        console.log(`✅ Kết thúc Plan ${plan.id} → force cập nhật finished 100% nếu cần.`);
        await updatePlanQuantityDone(plan.id, totalQty);
        await updatePlanStatus(plan.id, 'finished');
        await updateHistory(plan, totalQty);
        await markPlanCompleted(plan);

        io.emit('machine-data', {
            machine_id: plan.machine_id,
            plan_id: plan.id,
            product_id: plan.product_id,
            current_product: plan.product_name || plan.product_id,
            process_id: plan.process_id,
            process_name: plan.process_name || plan.process_id,
            lot_number: plan.lot_number,
            quantity_total: totalQty,
            quantity_done: totalQty,
            status: 'finished',
            start_time: plan.start_time,
            end_time: plan.end_time,
            timestamp: new Date().toISOString(),
            progress: 100
        });

        const productProgress = await getProductProgress(plan.product_id);
        io.emit('product-progress', {
            product_id: plan.product_id,
            progress: productProgress
        });
        return;
    }


    await markPlanCompleted(plan);
}


io.on('connection', async socket => {
    console.log(`📡 Client connected: ${socket.id}`);

    const orderProgressInterval = setInterval(() => emitAllOrderProgress(), 5000);
    const machineDataInterval = setInterval(() => emitMachineStates(), 3000);
    const productProgressInterval = setInterval(() => emitProductProgress(), 5000);

    if (!machineStates['running']) {
        machineStates['running'] = true;
        while (true) {
            const plans = await getNextPlannedPlans();
            const overduePlans = await getOverdueWorkingPlans();

            // Ưu tiên chạy overdue trước:
            if (overduePlans.length) {
                console.log(`⚠️ Có ${overduePlans.length} plan quá hạn → force cập nhật...`);
                await Promise.all(overduePlans.map(plan => simulateProduction(plan)));
            }

            // Sau đó xử lý plan mới:
            if (plans.length) {
                console.log(`🚀 Đang chạy ${plans.length} plan song song...`);
                await Promise.all(plans.map(plan => simulateProduction(plan)));
            }

            // Sleep nhẹ sau vòng lặp:
            await sleep(300);
        }

    }


    socket.on('disconnect', () => {
        console.log(`❌ Client disconnected: ${socket.id}`);
        clearInterval(orderProgressInterval);
        clearInterval(machineDataInterval);
        clearInterval(productProgressInterval);
    });
});


const PORT = 3001;
server.listen(PORT, () => {
    console.log(`🚀 WebSocket server đang chạy tại http://localhost:${PORT}`);
});
