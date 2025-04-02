<template>
    <div class="container mt-4">
      <h2>📦 Danh sách Kế hoạch Sản Xuất</h2>
  
      <table class="table table-bordered mt-3">
        <thead>
          <tr>
            <th>ID</th>
            <th>Sản phẩm</th>
            <th>Khách hàng</th>
            <th>Ngày đặt</th>
            <th>Ngày giao</th>
            <th>Trạng thái</th>
            <th>Chi tiết</th>
          </tr>
        </thead>
        <tbody>
          <template v-for="order in productionOrders" :key="order.id">
            <tr>
              <td>{{ order.id }}</td>
              <td>{{ order.product_name || 'Không xác định' }}</td>
              <td>{{ order.customer_name || 'Không xác định' }}</td>
              <td>{{ formatDate(order.start_date) }}</td>
              <td>{{ formatDate(order.end_date) }}</td>
              <td>{{ order.status }}</td>
              <td>
                <button class="btn btn-sm btn-info" @click="toggleDetail(order.id)">
                  👁️ Xem chi tiết
                </button>
              </td>
            </tr>
            <tr v-if="selectedOrderId === order.id">
              <td colspan="7">
                <ProductionPlans :orderId="order.id" />
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </template>
  
  <script>
  import { mapState, mapActions } from 'vuex';
  import ProductionPlans from '@/components/ProductionPlans.vue';
  
  export default {
    components: { ProductionPlans },
    data() {
      return {
        selectedOrderId: null
      };
    },
    computed: {
      ...mapState('productionOrders', ['productionOrders'])
    },
    methods: {
      ...mapActions('productionOrders', ['fetchProductionOrders']),
      formatDate(dateStr) {
        if (!dateStr) return 'Không xác định';
        const date = new Date(dateStr + 'T00:00:00');
        return isNaN(date) ? 'Không xác định' : date.toLocaleDateString('vi-VN');
      },
      toggleDetail(orderId) {
        this.selectedOrderId = this.selectedOrderId === orderId ? null : orderId;
      }
    },
    mounted() {
      this.fetchProductionOrders();
    }
  };
  </script>
  