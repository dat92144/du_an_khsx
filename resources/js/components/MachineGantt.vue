<template>
  <div ref="gantt" style="width: 100%; height: 600px;"></div>
</template>

<script>
export default {
  name: "MachineGantt",
  props: ["tasks", "links"],
  mounted() {
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
    gantt.clearAll();
    gantt.parse({ data: this.tasks, links: this.links || [] });
  }
};
</script>
