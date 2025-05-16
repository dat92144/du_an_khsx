<template>
  <div ref="gantt" style="width: 100%; height: 600px;"></div>
</template>

<script>
export default {
  name: "ProductGantt",
  props: ["tasks", "links"],
  mounted() {
    const gantt = window.gantt;

    // Cấu hình Gantt
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

    // Init và parse chỉ 1 lần
    gantt.init(this.$refs.gantt);
    if (this.tasks && this.tasks.length) {
      gantt.clearAll();
      gantt.parse({ data: this.tasks, links: this.links || [] });
    }

    // Sự kiện click lô
    gantt.attachEvent("onTaskClick", async (id) => {
      if (id.startsWith("lot-")) {
        const [_, productId, lot] = id.split("-");
        await this.loadLotDetail(productId, lot);
      }
      return true;
    });
  },
  methods: {
    renderGantt() {
        const gantt = window.gantt;
        if (!this.$refs.gantt || !this.tasks.length) return;

        gantt.init(this.$refs.gantt);
        gantt.clearAll();
        gantt.parse({ data: this.tasks, links: this.links || [] });
    },

    async loadLotDetail(productId, lot) {
      try {
        const res = await fetch(`/api/gantt/lot-detail?product_id=${productId}&lot=${lot}`, {
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
    }
  }
};
</script>
