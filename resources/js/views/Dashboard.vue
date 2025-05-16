<template>
  <div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
      <BarChart class="w-6 h-6" /> Dashboard
    </h2>

    <!-- 📊 Thống kê -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
      <DashboardCard title="Tổng Đơn Hàng" :value="stats.total_orders" :icon="icons.Package" />
      <DashboardCard title="Sản phẩm SX" :value="stats.total_products" :icon="icons.Cog" />
      <DashboardCard title="Nhà Cung Cấp" :value="stats.total_suppliers" :icon="icons.Factory" />
    </div>

    <!-- 🎛️ Tùy chọn hiển thị Gantt -->
    <div class="mb-4">
      <label class="font-semibold mr-2">Hiển thị kế hoạch theo:</label>
      <select v-model="selectedMode" class="border px-3 py-2 rounded">
        <option value="default">🔷 Gantt mặc định</option>
        <option value="order">🔵 Đơn hàng</option>
        <option value="machine">🟢 Máy móc</option>
        <option value="product">🟣 Sản phẩm</option>
        <option value="batch">🟠 Lô sản xuất</option>
        <option value="overview">⚪ Tổng quan</option>
      </select>
    </div>

    <!-- Gantt hiển thị theo chế độ -->
    <OrderGantt
      v-if="selectedMode === 'order'"
      :tasks="orderTasks"
      :links="orderLinks"
    />

    <MachineGantt v-if="selectedMode === 'machine'" :tasks="machineTasks" :links="[]" />

    <ProductGantt
        v-if="selectedMode === 'product'"
        ref="productGantt"
        :tasks="productTasks"
        :links="[]"
        @show-lot-gantt="handleShowLotGantt"
    />


    <BatchGantt v-if="selectedMode === 'batch'" :tasks="[]" :links="[]" />
    <OverviewGantt v-if="selectedMode === 'overview'" :tasks="[]" :links="[]" />

    <!-- Modal chi tiết công đoạn của lô -->
    <GanttDetailModal
      :visible="showLotModal"
      :tasks="lotTasks"
      :title="lotTitle"
      @close="showLotModal = false"
    />
  </div>
</template>

<script>
import DashboardCard from '../components/DashboardCard.vue';
import OrderGantt from '../components/OrderGantt.vue';
import ProductGantt from '../components/ProductGantt.vue';
import MachineGantt from '../components/MachineGantt.vue';
import BatchGantt from '../components/BatchGantt.vue';
import OverviewGantt from '../components/OverviewGantt.vue';
import GanttDetailModal from '../components/GanttDetailModal.vue';
import { BarChart, Package, Cog, Factory } from 'lucide-vue-next';
import { mapState, mapActions, mapGetters } from 'vuex';
import axios from 'axios';

export default {
  components: {
    DashboardCard,
    OrderGantt,
    ProductGantt,
    MachineGantt,
    BatchGantt,
    OverviewGantt,
    GanttDetailModal,
    BarChart
  },
  data() {
    return {
      selectedMode: 'default',
      stats: {
        total_orders: 0,
        total_products: 0,
        total_suppliers: 0
      },
      icons: {
        Package,
        Cog,
        Factory
      },
      showLotModal: false,
      lotTasks: [],
      lotTitle: ""
    };
  },
  computed: {
    ...mapState('gantt', {
      orderTasks: state => state.orderGantt.data,
      orderLinks: state => state.orderGantt.links
    }),
    ...mapGetters('gantt', ['productTasks', 'machineTasks'])
  },
  mounted() {
    this.loadDashboardData();
  },
  watch: {
    showLotModal(newVal) {
        if (!newVal && this.selectedMode === 'product') {
            this.restoreProductGantt();
        }
    }
 },
  methods: {
    ...mapActions('productionOrders', ['fetchProductionPlans']),
    ...mapActions('gantt', ['fetchOrderGantt', 'fetchProductGantt', 'fetchMachineGantt']),
    async loadDashboardData() {
      await Promise.all([
        this.fetchDashboardStats(),
        this.fetchProductionPlans(),
        this.fetchOrderGantt(),
        this.fetchProductGantt()
      ]);
    },
    async fetchDashboardStats() {
      try {
        const res = await axios.get('/api/dashboard', {
          headers: { Authorization: `Bearer ${localStorage.getItem("auth_token")}` }
        });
        this.stats = res.data;
      } catch (err) {
        console.error("❌ Lỗi khi tải dữ liệu dashboard:", err);
      }
    },
    handleShowLotGantt({ title, tasks }) {
      this.lotTitle = title;
      this.lotTasks = tasks;
      this.showLotModal = true;
    },
    restoreProductGantt() {
        this.$nextTick(() => {
        // Gọi lại render của ProductGantt nếu cần
        const ganttComp = this.$refs.productGantt;
        if (ganttComp && ganttComp.renderGantt) {
            ganttComp.renderGantt();
        }
        });
    },
    async loadDashboardData() {
        await Promise.all([
        this.fetchDashboardStats(),
        this.fetchProductionPlans(),
        this.fetchOrderGantt(),
        this.fetchProductGantt(),
        this.fetchMachineGantt()
        ]);
    }
  }
};
</script>
