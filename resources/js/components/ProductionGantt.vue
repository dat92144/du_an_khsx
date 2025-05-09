<template>
  <div ref="gantt" style="width: 100%; height: 600px;"></div>
</template>

<script>
export default {
  name: "ProductionGantt",
  props: ["tasks", "links"],

  mounted() {
    const gantt = window.gantt;

    // ✅ Định dạng thời gian từ DB
    gantt.config.date_format = "%Y-%m-%d %H:%i:%s";

    // ✅ Chia timeline theo ngày và giờ
    gantt.config.scale_unit = "day";
    gantt.config.scales = [
      { unit: "day", step: 1, format: "%d %M, %Y" },
      { unit: "hour", step: 1, format: "%H:%i" }
    ];
    gantt.config.step = 1;
    gantt.config.duration_unit = "minute";

    // ✅ Tooltip hiển thị chi tiết
    gantt.templates.tooltip_text = function (start, end, task) {
      return `
        <b>${task.text}</b><br/>
        Bắt đầu: ${gantt.templates.tooltip_date_format(start)}<br/>
        Kết thúc: ${gantt.templates.tooltip_date_format(end)}<br/>
        Thời lượng: ${task.duration} phút
      `;
    };

    // ✅ Tô màu hàng nếu thời lượng quá dài
    gantt.templates.grid_row_class = function (start, end, task) {
      if (task.duration > 480) return "task-long"; // >8 tiếng
      return "";
    };

    // ✅ Cột hiển thị
    gantt.config.columns = [
      { name: "text", label: "Task name", tree: true, width: "*" },
      { name: "start_date", label: "Start", align: "center", width: 120 },
      { name: "duration", label: "Duration (min)", align: "center", width: 130 }
    ];

    // ✅ Giao diện nâng cao
    gantt.config.open_tree_initially = true;
    gantt.config.fit_tasks = false;
    gantt.config.auto_types = true;

    gantt.init(this.$refs.gantt);

    // ✅ Mở rộng timeline khi task kéo ra ngoài phạm vi
    gantt.attachEvent("onAfterTaskUpdate", (id, task) => {
      const start = new Date(task.start_date);
      const end = gantt.calculateEndDate(task);

      const currentStart = gantt.getState().min_date;
      const currentEnd = gantt.getState().max_date;

      let needUpdate = false;
      if (start < currentStart || end > currentEnd) needUpdate = true;

      if (needUpdate) {
        const newMin = new Date(Math.min(currentStart, start));
        newMin.setDate(newMin.getDate() - 1);

        const newMax = new Date(Math.max(currentEnd, end));
        newMax.setDate(newMax.getDate() + 1);

        gantt.setVisibleDate(newMin, newMax);
      }
    });

    // ✅ Load dữ liệu nếu có
    if (this.tasks && this.tasks.length) {
      gantt.parse({ data: this.tasks, links: this.links });

      // ✅ Tính và giới hạn timeline theo dữ liệu
      const { minDate, maxDate } = this.getDateRange(this.tasks);
      gantt.setVisibleDate(minDate, maxDate);
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
/* 💡 Tô nền vàng cho các task dài bất thường */
.task-long .gantt_cell,
.task-long .gantt_task_row {
  background-color: #fff3cd !important;
}
</style>
