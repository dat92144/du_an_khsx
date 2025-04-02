<template>
    <div class="card p-3 bg-light mt-3">
      <h5>📋 Lệnh sản xuất cho đơn hàng</h5>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Lot</th>
            <th>Sản phẩm</th>
            <th>Công đoạn</th>
            <th>Máy</th>
            <th>SL Lot</th>
            <th>Bắt đầu</th>
            <th>Kết thúc</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="plan in plans" :key="plan.plan_id">
            <td>{{ plan.lot_number }}</td>
            <td>{{ plan.product?.name || plan.product_id }}</td>
            <td>{{ plan.process?.name || plan.process_id }}</td>
            <td>{{ plan.machine?.name || plan.machine_id }}</td>
            <td>{{ plan.lot_size }}</td>
            <td>{{ formatDate(plan.start_time) }}</td>
            <td>{{ formatDate(plan.end_time) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  export default {
    props: ['orderId'],
    data() {
      return {
        plans: []
      };
    },
    watch: {
      orderId: {
        immediate: true,
        handler(newVal) {
          if (newVal) this.loadPlans(newVal);
        }
      }
    },
    methods: {
      async loadPlans(orderId) {
        try {
          const res = await axios.get(`/api/orders/${orderId}/plans`, {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
          });
          this.plans = res.data;
        } catch (err) {
          console.error('❌ Không tải được lệnh sản xuất:', err);
        }
      }
    }
  };
  </script>  
  