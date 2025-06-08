<template>
  <div>
    <div class="flex justify-end mb-2">
      <button @click="exportToExcel" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
        📊 Xuất Excel
      </button>
    </div>
    <div ref="gantt" style="width: 100%; height: 600px; min-height: 300px;"></div>
  </div>
</template>

<script>
import io from "socket.io-client";
import * as XLSX from "xlsx";

export default {
  name: "ProductGantt",
  emits: ["show-lot-gantt"],
  props: ["tasks", "links"],

  data() {
    return {
      localTasks: [],
      socket: null,
      ganttInited: false,
      observer: null
    };
  },

  mounted() {
    this.localTasks = [...this.tasks];

    // Setup socket
    this.socket = io("http://localhost:3001");

    this.socket.on("product-progress", ({ product_id, progress }) => {
      const gantt = window.gantt;
      const taskId = product_id;
      if (gantt.isTaskExists(taskId)) {
        const task = gantt.getTask(taskId);
        task.progress = progress;
        gantt.updateTask(taskId);
      }
    });

    // Setup MutationObserver để đảm bảo renderGantt khi visible
    this.observer = new MutationObserver(() => {
      if (this.$refs.gantt && this.$refs.gantt.offsetHeight > 0 && !this.ganttInited) {
        console.log("🟢 ProductGantt container visible → initGantt()");
        this.renderGantt();
      }
    });

    this.observer.observe(this.$refs.gantt, { attributes: true, attributeFilter: ['style', 'class'] });
  },

  watch: {
    tasks(newVal) {
      this.localTasks = [...newVal];
      this.$nextTick(() => this.renderGantt());
    }
  },

  beforeDestroy() {
    if (this.socket) {
      this.socket.disconnect();
      this.socket = null;
    }
    if (this.observer) {
      this.observer.disconnect();
      this.observer = null;
    }
  },

  methods: {
    initGantt() {
      if (this.ganttInited) return;

      const gantt = window.gantt;

      gantt.config.date_format = "%Y-%m-%d %H:%i:%s";
      gantt.config.duration_unit = "hour";
      gantt.config.scale_height = 60;
      gantt.config.min_column_width = 80;

      gantt.config.scales = [
        { unit: "day", step: 1, format: "%d %M, %Y" },
        { unit: "hour", step: 1, format: "%H:%i" }
      ];

      gantt.config.columns = [
        { name: "text", label: "Sản phẩm / Lô", tree: true, width: "*" },
        { name: "start_date", label: "Bắt đầu", align: "center" },
        { name: "duration", label: "Thời lượng (giờ)", align: "center" },
        {
          name: "progress",
          label: "Tiến độ",
          align: "center",
          template: task => `${Math.round((task.progress || 0) * 100)}%`
        }
      ];

      gantt.config.open_tree_initially = false;
      gantt.config.fit_tasks = true;
      gantt.config.auto_types = true;

      gantt.init(this.$refs.gantt);
      this.ganttInited = true;

      // 👉 attachEvent phải bind SAU initGantt() để luôn có hiệu lực
      gantt.attachEvent("onTaskClick", async (id) => {
        if (id.startsWith("lot-")) {
          const [_, orderId, lot, type, productId] = id.split("-");
          await this.loadLotDetail(productId, lot, type);
        }
        return true;
      });
    },

    renderGantt() {
      const gantt = window.gantt;
      if (!gantt) return;

      // Chỉ init khi container visible
      if (!this.ganttInited && this.$refs.gantt.offsetHeight > 0) {
        this.initGantt();
      }

      if (!this.localTasks.length || !this.ganttInited) return;

      gantt.clearAll();
      gantt.parse({ data: this.localTasks, links: this.links || [] });
    },

    async loadLotDetail(productId, lot, type) {
      try {
        const res = await fetch(`/api/gantt/lot-detail?product_id=${productId}&lot=${lot}&type=${type}`, {
          headers: { Authorization: `Bearer ${localStorage.getItem("auth_token")}` }
        });
        const data = await res.json();
        this.$emit("show-lot-gantt", {
          title: `Chi tiết Lô ${lot} - ${productId}`,
          tasks: data.data
        });
      } catch (err) {
        console.error("Lỗi khi lấy chi tiết lô:", err);
      }
    },

    exportToExcel() {
      const gantt = window.gantt;
      const data = gantt.serialize().data;

      const worksheetData = data.map(task => ({
        "ID": task.id,
        "Tên nhiệm vụ": task.text,
        "Bắt đầu": task.start_date,
        "Thời lượng (giờ)": task.duration,
        "Tiến độ (%)": Math.round((task.progress || 0) * 100),
        "Gantt cha": task.parent || ""
      }));

      const worksheet = XLSX.utils.json_to_sheet(worksheetData);
      const workbook = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(workbook, worksheet, "Tiến độ sản xuất");

      XLSX.writeFile(workbook, "bao-cao-tien-do.xlsx");
    }
  }
};
</script>
