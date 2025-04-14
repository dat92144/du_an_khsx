<template>
    <div class="container mx-auto p-6">
      <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
        <BarChart class="w-6 h-6" /> Dashboard
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <DashboardCard title="Tổng Đơn Hàng" :value="stats.total_orders" :icon="icons.Package" />
        <DashboardCard title="Sản phẩm SX" :value="stats.total_products" :icon="icons.Cog" />
        <DashboardCard title="Nhà Cung Cấp" :value="stats.total_suppliers" :icon="icons.Factory" />
      </div>
    </div>
  </template>

  <script>
  import DashboardCard from '../components/DashboardCard.vue';
  import { BarChart, Package, Cog, Factory } from 'lucide-vue-next';
  import axios from 'axios';

  export default {
    components: {
      DashboardCard,
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
      icons() {
        return {
          Package,
          Cog,
          Factory
        };
      }
    },
    async mounted() {
      await this.fetchDashboardStats();
    },
    methods: {
      async fetchDashboardStats() {
        try {
          const response = await axios.get('/api/dashboard', {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
          });
          this.stats = response.data;
        } catch (error) {
          console.error('Lỗi khi lấy dữ liệu Dashboard:', error);
        }
      }
    }
  };
  </script>
