<template>
  <div v-if="visible" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-4/5 h-4/5 relative">
      <div class="flex justify-between items-center px-4 py-3 border-b">
        <h2 class="text-xl font-semibold">{{ title }}</h2>
        <button @click="$emit('close')" class="text-red-500 hover:text-red-700 text-xl font-bold">✕</button>
      </div>
      <div v-show="visible" ref="ganttDetail" style="width: 100%; height: calc(100% - 60px); padding: 10px;"></div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    visible: { type: Boolean, default: false },
    title: { type: String, default: "Chi tiết Lô" },
    tasks: { type: Array, default: () => [] }
  },
  watch: {
    visible(newVal) {
      if (newVal) {
        this.$nextTick(() => {
          this.renderGantt();
        });
      }
    }
  },
  methods: {
    renderGantt() {
      const gantt = window.gantt;
      if (!this.$refs.ganttDetail || !this.tasks.length) return;

      gantt.clearAll(); // Xóa dữ liệu cũ
      gantt.init(this.$refs.ganttDetail);
      gantt.parse({ data: this.tasks, links: [] });
    }
  }
};
</script>
