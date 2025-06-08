<template>
  <div ref="gantt" style="width: 100%; height: 600px; min-height: 300px;"></div>
</template>

<script>
import io from "socket.io-client";

export default {
  name: "OrderGantt",
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

    this.socket.on("order-progress", ({ order_id, product_id, progress }) => {
      const gantt = window.gantt;
      const taskId = order_id;
      if (gantt.isTaskExists(taskId)) {
        const task = gantt.getTask(taskId);
        task.progress = progress;
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
      gantt.config.duration_unit = "day";
      gantt.config.step = 1;
      gantt.config.scale_height = 60;
      gantt.config.min_column_width = 100;

      gantt.config.scales = [
        { unit: "day", step: 1, format: "%d %M, %Y" }
      ];

      gantt.config.columns = [
        { name: "text", label: "Tên", tree: true, width: 150 },
        { name: "start_date", label: "Bắt đầu", align: "center", width: 120 },
        { name: "duration", label: "Thời lượng", align: "center", width: 120 },
        {
          name: "progress",
          label: "Tiến độ",
          align: "center",
          width: 100,
          template: task => `${Math.round((task.progress || 0) * 100)}%`
        }
      ];

      gantt.templates.tooltip_text = (start, end, task) => `
        <b>${task.text}</b><br/>
        Bắt đầu: ${gantt.templates.tooltip_date_format(start)}<br/>
        Kết thúc: ${gantt.templates.tooltip_date_format(end)}<br/>
        Thời lượng: ${task.duration} ngày<br/>
        Tiến độ: ${Math.round((task.progress || 0) * 100)}%
      `;

      gantt.config.open_tree_initially = true;
      gantt.config.fit_tasks = false;
      gantt.config.autoscroll = true;
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
