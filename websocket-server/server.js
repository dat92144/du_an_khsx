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

// ✅ Cập nhật tiến độ toàn bộ đơn hàng mỗi 10s
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

async function markPlanCompleted(plan) {
  const conn = await mysql.createConnection(dbConfig);
  await conn.execute(`UPDATE production_plans SET status = 'completed' WHERE id = ?`, [plan.id]);

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
    WHERE order_id = ? AND product_id = ? AND status = 'completed'
  `, [plan.order_id, plan.product_id]);

  const total = allPlans[0].total;
  const completed = completedPlans[0].completed;

  if (total !== completed) {
    console.log(`⚠️ Chưa hoàn thành toàn bộ lệnh sản xuất cho order_id = ${plan.order_id}`);
    await conn.end();
    return;
  }

  const itemType = plan.product_id ? 'product' : 'semi_finished_product';
  const itemId = plan.product_id ?? plan.semi_finished_product_id;

  const [orderDetailRows] = await conn.execute(`
    SELECT unit_id FROM order_details
    WHERE order_id = ? AND product_id = ?
    LIMIT 1
  `, [plan.order_id, plan.product_id]);

  const unitId = orderDetailRows.length ? orderDetailRows[0].unit_id : null;
  if (!unitId) {
    console.error(`❌ Không tìm thấy unit_id`);
    await conn.end();
    return;
  }

  await conn.execute(`
    INSERT INTO inventories (item_id, item_type, quantity, unit_id, last_updated)
    VALUES (?, ?, ?, ?, NOW())
    ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity), last_updated = NOW()
  `, [itemId, itemType, plan.total_quantity, unitId]);

  console.log(`✅ Cập nhật tồn kho cho order_id = ${plan.order_id}`);
  await conn.end();
}

async function getOrderProgress(orderId, productId) {
  const conn = await mysql.createConnection(dbConfig);
  const [allPlans] = await conn.execute(`
    SELECT COUNT(*) AS total FROM production_plans
    WHERE order_id = ? AND product_id = ?
  `, [orderId, productId]);
  const [completedPlans] = await conn.execute(`
    SELECT COUNT(*) AS completed FROM production_plans
    WHERE order_id = ? AND product_id = ? AND status = 'completed'
  `, [orderId, productId]);
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
      status: produced === totalQty ? 'completed' : 'working',
      start_time: plan.start_time,
      end_time: plan.end_time,
      timestamp: new Date().toISOString(),
      progress
    };

    io.emit('machine-data', payload);
    await updateHistory(plan, produced);
    await sleep(intervalMs);
  }

  await markPlanCompleted(plan);
}

io.on('connection', async socket => {
  console.log(`📡 Client connected: ${socket.id}`);

  // Gửi định kỳ tiến độ đơn hàng (dù client không tương tác)
  const interval = setInterval(() => emitAllOrderProgress(), 10000);

  // Nếu chưa chạy mô phỏng thì bắt đầu
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
    clearInterval(interval);
  });
});

const PORT = 3001;
server.listen(PORT, () => {
  console.log(`🚀 WebSocket server đang chạy tại http://localhost:${PORT}`);
});
