<template>
  <div ref="gantt" style="width: 100%; height: 600px; min-height: 300px;"></div>
</template>

<script>
import io from "socket.io-client";

export default {
  name: "MachineGantt",
  props: ["tasks", "links"],

  data() {
    return {
      localTasks: [],
      socket: null,
      ganttInited: false
    };
  },

  mounted() {
    this.localTasks = [...this.tasks];

    this.socket = io("http://localhost:3001");

    this.socket.on("machine-data", ({ machine_id, plan_id, progress }) => {
      const gantt = window.gantt;
      const taskId = plan_id;
      if (gantt.isTaskExists(taskId)) {
        const task = gantt.getTask(taskId);
        task.progress = progress / 100;
        gantt.updateTask(taskId);
      }
    });
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
        { name: "text", label: "Máy / Lệnh SX", tree: true, width: "*" },
        { name: "start_date", label: "Bắt đầu", align: "center" },
        { name: "duration", label: "Thời lượng (giờ)", align: "center" },
        {
          name: "progress",
          label: "Tiến độ",
          align: "center",
          template: task => `${Math.round((task.progress || 0) * 100)}%`
        }
      ];

      gantt.config.open_tree_initially = true;
      gantt.config.fit_tasks = true;
      gantt.config.auto_types = true;

      gantt.init(this.$refs.gantt);
      this.ganttInited = true;
    },

    renderGantt() {
      const gantt = window.gantt;
      if (!gantt) return;

      if (!this.ganttInited && this.$refs.gantt.offsetHeight > 0) {
        requestAnimationFrame(() => {
          this.initGantt();
          if (this.localTasks.length) {
            gantt.clearAll();
            gantt.parse({ data: this.localTasks, links: this.links || [] });
          }
        });
        return;
      }

      if (!this.localTasks.length || !this.ganttInited) return;

      gantt.clearAll();
      gantt.parse({ data: this.localTasks, links: this.links || [] });
    }
  }
};
</script>
