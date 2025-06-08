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

const dbConfig = {
  host: 'localhost',
  user: 'root',
  password: '',
  database: 'khsx'
};

let machineStates = {};

async function emitAllOrderProgress() {
  const conn = await mysql.createConnection(dbConfig);
  const [orders] = await conn.execute(`
    SELECT DISTINCT order_id, product_id
    FROM production_plans
    WHERE order_id IS NOT NULL AND product_id IS NOT NULL
  `);
  await conn.end();

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
  const conn = await mysql.createConnection(dbConfig);
  const [runningPlans] = await conn.execute(`
    SELECT * FROM production_plans
    WHERE status = 'working'
  `);
  await conn.end();

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
  const conn = await mysql.createConnection(dbConfig);
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

  await conn.end();
}

async function getNextPlannedPlan() {
  const conn = await mysql.createConnection(dbConfig);
  const [rows] = await conn.execute(`
    SELECT
      pp.*, ms.machine_id, ms.production_order_id,
      p.name AS product_name, sp.name AS process_name
    FROM production_plans pp
    JOIN machine_schedules ms ON ms.machine_id = pp.machine_id
    LEFT JOIN products p ON pp.product_id = p.id
    LEFT JOIN specs sp ON sp.process_id = pp.process_id AND sp.product_id = pp.product_id
    WHERE pp.status = 'planned'
    ORDER BY pp.order_id, pp.lot_number, pp.start_time
    LIMIT 1
  `);
  await conn.end();
  return rows.length ? rows[0] : null;
}

async function updateHistory(plan, quantity) {
  const conn = await mysql.createConnection(dbConfig);
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

  await conn.end();
}

async function updatePlanStatus(planId, status) {
  const conn = await mysql.createConnection(dbConfig);
  await conn.execute(`UPDATE production_plans SET status = ? WHERE id = ?`, [status, planId]);
  await conn.end();
}

async function updatePlanQuantityDone(planId, quantity) {
  const conn = await mysql.createConnection(dbConfig);
  await conn.execute(`UPDATE production_plans SET quantity_done = ? WHERE id = ?`, [quantity, planId]);
  await conn.end();
}

async function updateInventoryOnPlanFinish(plan) {
  const conn = await mysql.createConnection(dbConfig);

  const [bomRows] = await conn.execute(`
    SELECT * FROM bom_items
    WHERE product_id = ? AND process_id = ?
  `, [plan.product_id, plan.process_id]);

  for (const bom of bomRows) {
    if (bom.output_id) {
      await conn.execute(`
        INSERT INTO inventories (item_id, item_type, quantity, unit_id, last_updated)
        VALUES (?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE
          quantity = quantity + VALUES(quantity),
          last_updated = NOW()
      `, [
        bom.output_id,
        bom.output_type,
        bom.quantity_output * plan.total_quantity,
        bom.output_unit_id
      ]);
      console.log(`✅ [Inventory] + ${bom.quantity_output * plan.total_quantity} → ${bom.output_id} (${bom.output_type})`);
    }

    if (bom.input_material_id) {
      await conn.execute(`
        UPDATE inventories
        SET quantity = GREATEST(0, quantity - ?), last_updated = NOW()
        WHERE item_id = ? AND item_type = 'materials'
      `, [
        bom.quantity_input * plan.total_quantity,
        bom.input_material_id
      ]);
      console.log(`✅ [Inventory] - ${bom.quantity_input * plan.total_quantity} từ ${bom.input_material_id}`);
    }
  }

  await conn.end();
}

async function markPlanCompleted(plan) {
  const conn = await mysql.createConnection(dbConfig);
  await conn.execute(`UPDATE production_plans SET status = 'finished' WHERE id = ?`, [plan.id]);

  const orderProgress = await getOrderProgress(plan.order_id, plan.product_id);
  io.emit('order-progress', {
    order_id: plan.order_id,
    product_id: plan.product_id,
    progress: orderProgress
  });

  const [allPlans] = await conn.execute(`
    SELECT COUNT(*) AS total FROM production_plans
    WHERE order_id = ? AND product_id = ?
  `, [plan.order_id, plan.product_id]);
  const [completedPlans] = await conn.execute(`
    SELECT COUNT(*) AS completed FROM production_plans
    WHERE order_id = ? AND product_id = ? AND status = 'finished'
  `, [plan.order_id, plan.product_id]);

  const total = allPlans[0].total;
  const completed = completedPlans[0].completed;

  if (total !== completed) {
    console.log(`⚠️ Chưa hoàn thành toàn bộ lệnh sản xuất cho order_id = ${plan.order_id}`);
    await conn.end();
    return;
  }

  await conn.execute(`UPDATE orders SET producing_status = 'completed' WHERE id = ?`, [plan.order_id]);
  console.log(`✅ Đơn hàng ${plan.order_id} đã hoàn thành → cập nhật producing_status = completed`);

  await emitAllOrderProgress();
  await emitProductProgress();
  await updateInventoryOnPlanFinish(plan);

  await conn.end();
}

async function getOrderProgress(orderId, productId) {
  const conn = await mysql.createConnection(dbConfig);

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
  await conn.end();

  const total = allPlans[0].total;
  const completed = completedPlans[0].completed;
  return total === 0 ? 0 : completed / total;
}

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

async function simulateProduction(plan) {
  const durationMin = moment(plan.end_time).diff(moment(plan.start_time), 'minutes');
  const totalQty = plan.total_quantity;
  const intervalMs = (durationMin * 60 * 1000) / totalQty;

  await updatePlanStatus(plan.id, 'working');

  for (let produced = 1; produced <= totalQty; produced++) {
    const progress = Math.floor((produced / totalQty) * 100);
    const payload = {
      machine_id: plan.machine_id,
      plan_id: plan.id,
      product_id: plan.product_id,
      current_product: plan.product_name || plan.product_id,
      process_id: plan.process_id,
      process_name: plan.process_name || plan.process_id,
      lot_number: plan.lot_number,
      quantity_total: totalQty,
      quantity_done: produced,
      status: produced === totalQty ? 'finished' : 'working',
      start_time: plan.start_time,
      end_time: plan.end_time,
      timestamp: new Date().toISOString(),
      progress
    };

    io.emit('machine-data', payload);

    await updateHistory(plan, produced);
    await updatePlanQuantityDone(plan.id, produced);

    if (produced === totalQty) {
      await updatePlanStatus(plan.id, 'finished');
    }
    const productProgress = await getProductProgress(plan.product_id);
        io.emit('product-progress', {
        product_id: plan.product_id,
        progress: productProgress
    });
    await sleep(intervalMs);
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
      const plan = await getNextPlannedPlan();
      if (plan) {
        await simulateProduction(plan);
      } else {
        console.log('🟡 Không có kế hoạch nào để mô phỏng. Chờ 5 giây...');
        await sleep(5000);
      }
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
