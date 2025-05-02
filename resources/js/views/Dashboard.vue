<template>
  <div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
      <BarChart class="w-6 h-6" /> Dashboard
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
      <DashboardCard title="Tổng Đơn Hàng" :value="stats.total_orders" :icon="icons.Package" />
      <DashboardCard title="Sản phẩm SX" :value="stats.total_products" :icon="icons.Cog" />
      <DashboardCard title="Nhà Cung Cấp" :value="stats.total_suppliers" :icon="icons.Factory" />
    </div>

    <h3 class="text-lg font-semibold my-4">Kế hoạch sản xuất</h3>
    <ProductionGantt :tasks="tasks" :links="links" :key="tasks.length" />
  </div>
</template>

<script>
import DashboardCard from '../components/DashboardCard.vue';
import ProductionGantt from '../components/ProductionGantt.vue';
import { BarChart, Package, Cog, Factory } from 'lucide-vue-next';
import { mapState, mapActions } from 'vuex';
import axios from 'axios';

export default {
  components: {
    DashboardCard,
    ProductionGantt,
    BarChart
  },
  data() {
    return {
      stats: {
        total_orders: 0,
        total_products: 0,
        total_suppliers: 0
      }
    };
  },
  computed: {
    ...mapState('productionOrders', {
      tasks: state => state.productionPlans?.data ?? [],
      links: state => state.productionPlans?.links ?? []
    }),
    icons() {
      return {
        Package,
        Cog,
        Factory
      };
    }
  },
  mounted() {
    this.fetchDashboardStats();
    this.fetchProductionPlans();
  },
  methods: {
    ...mapActions('productionOrders', ['fetchProductionPlans']),

    async fetchDashboardStats() {
      try {
        const response = await axios.get('/api/dashboard', {
          headers: { Authorization: `Bearer ${localStorage.getItem("auth_token")}` }
        });
        this.stats = response.data;
      } catch (error) {
        console.error("Lỗi khi lấy dữ liệu Dashboard:", error);
      }
    }
  }
};
</script>
