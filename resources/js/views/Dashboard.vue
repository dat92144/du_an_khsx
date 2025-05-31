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
      v-if="selectedMode === 'order'"
      ref="orderGantt"
      :tasks="filteredTasks"
      :links="orderLinks"
    />

    <MachineGantt
      v-if="selectedMode === 'machine'"
      ref="machineGantt"
      :tasks="filteredTasks"
      :links="[]"
    />

    <ProductGantt
      v-if="selectedMode === 'product'"
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

        // Lưu vào map để dễ truy cập
        for (const task of tasks) {
            taskMap[task.id] = task;
        }

        // Xác định task phù hợp
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
            // Giữ lại task cha
            if (task.parent) matchedIds.add(task.parent);
            }
        }

        // 🔄 Duyệt để giữ lại cả cha lẫn con
        return tasks.filter(t => matchedIds.has(t.id));
    }

  },
  watch: {
    showLotModal(newVal) {
      if (!newVal && this.selectedMode === 'product') {
        this.restoreProductGantt();
      }
    },
    filteredTasks() {
      this.refreshVisibleGantt();
    }
  },
  mounted() {
    this.loadDashboardData();
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

    restoreProductGantt() {
      this.$nextTick(() => {
        const ganttComp = this.$refs.productGantt;
        if (ganttComp && ganttComp.renderGantt) {
          ganttComp.renderGantt();
        }
      });
    },

    refreshVisibleGantt() {
      this.$nextTick(() => {
        const refMap = {
          order: this.$refs.orderGantt,
          product: this.$refs.productGantt,
          machine: this.$refs.machineGantt
        };
        const ganttComp = refMap[this.selectedMode];
        if (ganttComp && ganttComp.renderGantt) {
          ganttComp.renderGantt();
        }
      });
    }
  }
};
</script>
