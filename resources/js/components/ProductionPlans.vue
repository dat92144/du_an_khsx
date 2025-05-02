<template>
  <div class="card p-3 bg-light mt-3">
    <h5 class="flex items-center gap-2 text-lg font-semibold mb-3">
      <ClipboardList class="w-5 h-5" /> Lệnh sản xuất cho đơn hàng
    </h5>

    <div v-if="loading" class="text-center my-4">
      Đang tải dữ liệu...
    </div>

    <div v-else>
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
          <tr v-if="productionPlans.length === 0">
            <td colspan="7" class="text-center">Không có dữ liệu</td>
          </tr>
          <tr v-for="plan in productionPlans" :key="plan.plan_id">
            <td>{{ plan.lot_number }}</td>
            <td>{{ plan.product && plan.product.name ? plan.product.name : plan.product_id }}</td>
            <td>{{ plan.process && plan.process.name ? plan.process.name : plan.process_id }}</td>
            <td>{{ plan.machine && plan.machine.name ? plan.machine.name : plan.machine_id }}</td>
            <td>{{ plan.lot_size }}</td>
            <td>{{ formatDate(plan.start_time) }}</td>
            <td>{{ formatDate(plan.end_time) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex';
import { ClipboardList } from 'lucide-vue-next';

export default {
  components: { ClipboardList },
  props: {
    orderId: {
      type: [String, Number],
      required: true
    }
  },
  data() {
    return {
      loading: false
    };
  },
  computed: {
    ...mapState('productionOrders', ['productionPlans'])
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
    ...mapActions('productionOrders', ['fetchPlansByOrder']),
    async loadPlans(orderId) {
      console.log("🔍 Đang tải plans cho orderId:", orderId);
      this.loading = true;
      await this.fetchPlansByOrder(orderId);
      this.loading = false;
    },
    formatDate(dateStr) {
      if (!dateStr) return '';
      const date = new Date(dateStr);
      return isNaN(date.getTime()) ? '' : date.toLocaleString();
    }
  }
};
</script>
