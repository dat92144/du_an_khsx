<template>
  <div ref="gantt" style="width: 100%; height: 600px;"></div>
</template>

<script>
export default {
  name: "ProductionGantt",
  props: ["tasks", "links"],

  mounted() {
    const gantt = window.gantt;

    // ✅ Cấu hình thời gian chi tiết
    gantt.config.date_format = "%Y-%m-%d %H:%i:%s";
    gantt.config.duration_unit = "minute";
    gantt.config.step = 1;
    gantt.config.scale_height = 60;
    gantt.config.min_column_width = 50;

    // ✅ Zoom chi tiết theo phút
    gantt.config.scales = [
      { unit: "hour", step: 1, format: "%H:%i" },
      { unit: "minute", step: 10, format: "%H:%i" }
    ];

    // ✅ Tooltip rõ ràng
    gantt.templates.tooltip_text = function (start, end, task) {
      return `
        <b>${task.text}</b><br/>
        Bắt đầu: ${gantt.templates.tooltip_date_format(start)}<br/>
        Kết thúc: ${gantt.templates.tooltip_date_format(end)}<br/>
        Thời lượng: ${task.duration} phút
      `;
    };

    // ✅ Tô màu task dài
    gantt.templates.grid_row_class = function (start, end, task) {
      if (task.duration > 480) return "task-long";
      return "";
    };

    // ✅ Cột hiển thị rõ ràng
    gantt.config.columns = [
      { name: "text", label: "Task name", tree: true, width: "*" },
      { name: "start_date", label: "Start", align: "center", width: 120 },
      { name: "duration", label: "Duration (min)", align: "center", width: 130 }
    ];

    gantt.config.open_tree_initially = true;
    gantt.config.fit_tasks = false;
    gantt.config.auto_types = true;

    gantt.init(this.$refs.gantt);

    // ✅ Tự mở rộng khi kéo task vượt giới hạn
    gantt.attachEvent("onAfterTaskUpdate", (id, task) => {
      const start = new Date(task.start_date);
      const end = gantt.calculateEndDate(task);
      const currentStart = gantt.getState().min_date;
      const currentEnd = gantt.getState().max_date;

      if (start < currentStart || end > currentEnd) {
        const newMin = new Date(Math.min(currentStart, start));
        newMin.setDate(newMin.getDate() - 1);
        const newMax = new Date(Math.max(currentEnd, end));
        newMax.setDate(newMax.getDate() + 1);
        gantt.setVisibleDate(newMin, newMax);
      }
    });

    // ✅ Load data nếu có
    if (this.tasks && this.tasks.length) {
      gantt.parse({ data: this.tasks, links: this.links });

      // ✅ Fit timeline vừa dữ liệu
      setTimeout(() => {
        gantt.ext.zoomToFit();
      }, 100); // đợi parse xong mới fit
    }
  },

  methods: {
    getDateRange(tasks) {
      const dates = tasks.map(t => new Date(t.start_date));
      const minDate = new Date(Math.min(...dates));
      const maxDate = new Date(Math.max(...dates));
      minDate.setDate(minDate.getDate() - 1);
      maxDate.setDate(maxDate.getDate() + 1);
      return { minDate, maxDate };
    }
  }
};
</script>

<style scoped>
.task-long .gantt_cell,
.task-long .gantt_task_row {
  background-color: #fff3cd !important;
}
</style>
  