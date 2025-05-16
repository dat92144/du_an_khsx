<template>
  <div ref="gantt" style="width: 100%; height: 600px;"></div>
</template>

<script>
export default {
  name: "OrderGantt",
  props: ["tasks", "links"],

  mounted() {
    const gantt = window.gantt;

    // ⚙️ Cấu hình đơn vị ngày
    gantt.config.date_format = "%Y-%m-%d %H:%i:%s";
    gantt.config.duration_unit = "day";
    gantt.config.step = 1;
    gantt.config.scale_height = 60;
    gantt.config.min_column_width = 80;

    // 🗓️ Hiển thị theo ngày
    gantt.config.scales = [
      { unit: "day", step: 1, format: "%d %M, %Y" }
    ];

    // 💬 Tooltip chi tiết
    gantt.templates.tooltip_text = (start, end, task) => {
      return `
        <b>${task.text}</b><br/>
        Bắt đầu: ${gantt.templates.tooltip_date_format(start)}<br/>
        Kết thúc: ${gantt.templates.tooltip_date_format(end)}<br/>
        Thời lượng: ${task.duration} ngày<br/>
        Tiến độ: ${Math.round((task.progress || 0) * 100)}%
      `;
    };

    // 📊 Cột hiển thị
    gantt.config.columns = [
      { name: "text", label: "Tên", tree: true, width: "*" },
      { name: "start_date", label: "Bắt đầu", align: "center" },
      { name: "duration", label: "Thời lượng (ngày)", align: "center" },
      {
        name: "progress",
        label: "Tiến độ",
        align: "center",
        template: task => `${Math.round((task.progress || 0) * 100)}%`
      }
    ];

    // 🌲 Giao diện cây + auto fit
    gantt.config.open_tree_initially = true;
    gantt.config.fit_tasks = true;
    gantt.config.auto_types = true;

    // 🚀 Khởi tạo và vẽ
    gantt.init(this.$refs.gantt);
    this.renderGantt();
  },

  watch: {
    tasks: { handler() { this.renderGantt(); }, deep: true },
    links: { handler() { this.renderGantt(); }, deep: true }
  },

  methods: {
    renderGantt() {
      const gantt = window.gantt;
      if (this.tasks && this.tasks.length) {
        gantt.clearAll();
        gantt.parse({ data: this.tasks, links: this.links || [] });
      }
    }
  }
};
</script>
