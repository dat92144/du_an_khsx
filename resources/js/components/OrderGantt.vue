<template>
  <div ref="gantt" style="width: 100%; height: 600px;"></div>
</template>

<script>
export default {
  name: "OrderGantt",
  props: ["tasks", "links"],

  mounted() {
    this.initGantt();
    this.renderGantt(); // render lần đầu
  },

  methods: {
    initGantt() {
      const gantt = window.gantt;

      gantt.config.date_format = "%Y-%m-%d %H:%i:%s";
      gantt.config.duration_unit = "day";
      gantt.config.step = 1;
      gantt.config.scale_height = 60;
      gantt.config.min_column_width = 100;
      gantt.config.scales = [{ unit: "day", step: 1, format: "%d %M, %Y" }];
      gantt.config.columns = [
        { name: "text", label: "Tên", tree: true, width: "150" },
        { name: "start_date", label: "Bắt đầu", align: "center", width: "100" },
        { name: "duration", label: "Thời lượng", align: "center", width: "100" },
        {
          name: "progress",
          label: "Tiến độ",
          align: "center",
          width: "100",
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
    },

    renderGantt() {
      const gantt = window.gantt;
      if (!gantt) return;

      gantt.clearAll();
      gantt.parse({
        data: this.tasks || [],
        links: this.links || []
      });
    }
  }
};
</script>
