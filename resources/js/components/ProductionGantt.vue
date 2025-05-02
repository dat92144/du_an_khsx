<template>
  <div ref="gantt" style="width: 100%; height: 600px;"></div>
</template>

<script>

export default {
  name: "ProductionGantt",
  props: ["tasks", "links"],
  mounted() {
    const gantt = window.gantt;

    gantt.config.columns = [
      { name: "text", label: "Task name", tree: true, width: "*" },
      { name: "start_date", label: "Start", align: "center" },
      { name: "duration", label: "Duration", align: "center" }
    ];

    gantt.config.scales = [
      { unit: "month", step: 1, format: "%F, %Y" },
      { unit: "day", step: 1, format: "%d, %D" }
    ];

    gantt.config.open_tree_initially = true;
    gantt.init(this.$refs.gantt);

    if (this.tasks && this.tasks.length) {
      gantt.parse({ data: this.tasks, links: this.links });
    }
  }

};
</script>
