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

    <div class="mb-4">
      <label class="font-semibold mr-2">Hiển thị kế hoạch theo:</label>
      <select v-model="selectedMode" class="border px-3 py-2 rounded">
        <option value="default">🔷 Gantt mặc định</option>
        <option value="order">🔵 Đơn hàng</option>
        <option value="machine">🟢 Máy móc</option>
        <option value="product">🟣 Sản phẩm</option>
      </select>
    </div>

    <div class="mb-4">
      <label class="font-semibold mr-2">Tìm kiếm:</label>
      <input
        type="text"
        v-model="searchKeyword"
        placeholder="Nhập từ khóa để lọc Gantt..."
        class="border px-3 py-2 rounded w-full max-w-sm"
      />
    </div>

    <OrderGantt
      v-show="selectedMode === 'order'"
      ref="orderGantt"
      :tasks="filteredTasks"
      :links="orderLinks"
    />

    <MachineGantt
      v-show="selectedMode === 'machine'"
      ref="machineGantt"
      :tasks="filteredTasks"
      :links="[]"
    />

    <ProductGantt
      v-show="!showLotModal && selectedMode === 'product'"
      ref="productGantt"
      :tasks="filteredTasks"
      :links="[]"
      @show-lot-gantt="handleShowLotGantt"
    />

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
import GanttDetailModal from '../components/GanttDetailModal.vue';
import { BarChart, Package, Cog, Factory } from 'lucide-vue-next';
import { mapState, mapActions, mapGetters } from 'vuex';
import axios from 'axios';
import { io } from 'socket.io-client';

export default {
  components: {
    DashboardCard,
    OrderGantt,
    ProductGantt,
    MachineGantt,
    GanttDetailModal,
    BarChart
  },

  data() {
    return {
      selectedMode: 'default',
      searchKeyword: '',
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
    ...mapGetters('gantt', ['productTasks', 'machineTasks']),

    getCurrentTasks() {
      switch (this.selectedMode) {
        case 'order': return this.orderTasks;
        case 'product': return this.productTasks;
        case 'machine': return this.machineTasks;
        default: return [];
      }
    },

    filteredTasks() {
      const keyword = this.searchKeyword.toLowerCase();
      const tasks = this.getCurrentTasks;

      if (!this.searchKeyword) return tasks;

      const matchedIds = new Set();
      const taskMap = {};

      for (const task of tasks) {
        taskMap[task.id] = task;
      }

      for (const task of tasks) {
        const text = task.text?.toLowerCase() || "";
        const startDate = (task.start_date ? String(task.start_date) : "").toLowerCase();
        const duration = String(task.duration || "");
        const progress = String(Math.round((task.progress || 0) * 100));

        const match =
          text.includes(keyword) ||
          startDate.includes(keyword) ||
          duration.includes(keyword) ||
          progress.includes(keyword);

        if (match) {
          matchedIds.add(task.id);
          if (task.parent) matchedIds.add(task.parent);
        }
      }

      return tasks.filter(t => matchedIds.has(t.id));
    }
  },
  mounted() {
    this.loadDashboardData();

    // Thêm socket:
    this.socket = io('http://localhost:3001');

    this.socket.on('order-progress', ({ order_id, product_id, progress }) => {
        this.$store.commit('gantt/SET_ORDER_PROGRESS', { order_id, progress });
    });

    this.socket.on('product-progress', ({ product_id, progress }) => {
        this.$store.commit('gantt/SET_PRODUCT_PROGRESS', { product_id, progress });
    });

    this.socket.on('machine-data', ({ plan_id, progress }) => {
        this.$store.commit('gantt/SET_MACHINE_PLAN_PROGRESS', { plan_id, progress: progress / 100 });
    });
    },

    beforeUnmount() {
    if (this.socket) {
        this.socket.disconnect();
    }
 },

  watch: {
    showLotModal(newVal) {
      if (!newVal && this.selectedMode === 'product') {
        // Khi đóng modal → gọi refreshVisibleGantt để đảm bảo ProductGantt render lại
        this.$nextTick(() => {
          this.refreshVisibleGantt();
        });
      }
    },

    selectedMode() {
      this.refreshVisibleGantt();
    },

    filteredTasks() {
      this.refreshVisibleGantt();
    }
  },

  methods: {
    ...mapActions('productionOrders', ['fetchProductionPlans']),
    ...mapActions('gantt', [
      'fetchOrderGantt',
      'fetchProductGantt',
      'fetchMachineGantt'
    ]),

    async loadDashboardData() {
      await Promise.all([
        this.fetchDashboardStats(),
        this.fetchProductionPlans(),
        this.fetchOrderGantt(),
        this.fetchProductGantt(),
        this.fetchMachineGantt()
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

    refreshVisibleGantt() {
      this.$nextTick(() => {
        const refMap = {
          order: this.$refs.orderGantt,
          product: this.$refs.productGantt,
          machine: this.$refs.machineGantt
        };
        const ganttComp = refMap[this.selectedMode];
        if (ganttComp) {
          // Reset ganttInited → đảm bảo init lại khi tab active
          ganttComp.ganttInited = false;
          if (ganttComp.renderGantt) {
            ganttComp.renderGantt();
          }
        }
      });
    }
  }
};
</script>
