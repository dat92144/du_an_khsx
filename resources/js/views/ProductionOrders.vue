<template>
  <div class="container mt-4">
    <h2 class="flex items-center gap-2 text-xl font-bold mb-3">
      <PackageCheck class="w-5 h-5" /> Danh sách Kế hoạch Sản Xuất
    </h2>

    <table class="table table-bordered mt-3">
      <thead>
        <tr>
          <th>Mã đơn hàng</th>
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
            <td>{{ order.order_id }}</td>
            <td>{{ order.product_name || 'Không xác định' }}</td>
            <td>{{ order.customer_name || 'Không xác định' }}</td>
            <td>{{ formatDate(order.start_date) }}</td>
            <td>{{ formatDate(order.end_date) }}</td>
            <td>{{ order.producing_status || order.status }}</td>
            <td>
              <button class="btn btn-sm btn-info d-flex align-items-center gap-1" @click="toggleDetail(order.id)">
                <Eye class="w-4 h-4" /> Xem chi tiết
              </button>
              <button class="btn btn-sm btn-success d-flex align-items-center gap-1" @click="createOrder(order.id)">
                <PackageCheck class="w-4 h-4" /> Tạo lệnh
              </button>
            </td>
          </tr>
          <tr v-if="selectedOrderId === order.id">
            <td colspan="7">
              <ProductionPlans :orderId="order.order_id" />
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
import { PackageCheck, Eye } from 'lucide-vue-next';

export default {
  components: {
    ProductionPlans,
    PackageCheck,
    Eye
  },
  data() {
    return {
      selectedOrderId: null
    };
  },
  computed: {
    ...mapState('productionOrders', ['productionOrders'])
  },
  methods: {
    ...mapActions('productionOrders', ['fetchProductionOrders', 'createProductionOrder']),
    formatDate(dateStr) {
      if (!dateStr) return 'Không xác định';
      const date = new Date(dateStr);
      return isNaN(date) ? 'Không xác định' : date.toLocaleDateString('vi-VN');
    },
    toggleDetail(orderId) {
      this.selectedOrderId = this.selectedOrderId === orderId ? null : orderId;
    },
    createOrder(id) {
      if (confirm('Bạn có chắc muốn tạo lệnh sản xuất?')) {
        this.createProductionOrder(id);
      }
    }
  },
  mounted() {
    this.fetchProductionOrders();
  }
};
</script>
