<template>
  <div class="container mt-4">
    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
      <Activity class="w-5 h-5" /> Giám sát máy móc (WebSocket)
    </h2>

    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Mã máy</th>
          <th>Mã sản phẩm</th>
          <th>Tên sản phẩm</th>
          <th>Công đoạn</th>
          <th>Lot</th>
          <th>Trạng thái</th>
          <th>Sản xuất</th>
          <th>Tiến độ</th>
          <th>ETA</th>
          <th>Cập nhật</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="m in machines" :key="m.id">
          <td>{{ m.id }}</td>
          <td>{{ m.product_id }}</td>
          <td>{{ m.current_product }}</td>
          <td>{{ m.process_name }}</td>
          <td>{{ m.lot_number }}</td>
          <td>{{ getStatusText(m.status) }}</td>
          <td>{{ m.quantity_done }} / {{ m.quantity_total || '...' }}</td>
          <td>
            <div class="progress-bar">
              <div class="progress-inner" :style="{ width: getProgress(m) + '%' }">
                {{ getProgress(m) }}%
              </div>
            </div>
          </td>
          <td>{{ formatTime(m.end_time) }}</td>
          <td>{{ formatTime(m.timestamp) }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useStore } from 'vuex';
import { io } from 'socket.io-client';
import { Activity } from 'lucide-vue-next';

export default {
  components: { Activity },
  setup() {
    const store = useStore();
    const machinesDisplay = ref([]);
    let socket;

    const allMachines = computed(() => store.getters['machines/machines']);
    const realtimeMachineStatus = computed(() => store.getters['gantt/realtimeMachineStatus']);

    onMounted(async () => {
      // Load danh sách máy ban đầu
      await store.dispatch('machines/fetchMachines');

      machinesDisplay.value = allMachines.value.map(machine => ({
        id: machine.id,
        name: machine.name,
        status: 'idle',
        product_id: null,
        current_product: '',
        process_name: '',
        lot_number: '',
        quantity_done: 0,
        quantity_total: 0,
        end_time: '',
        timestamp: ''
      }));

      // 🚀 Load trạng thái realtime từ API
      await store.dispatch('gantt/fetchRealtimeMachineStatus');

      // Fill dữ liệu realtime vào machinesDisplay
      for (const m of realtimeMachineStatus.value) {
        const idx = machinesDisplay.value.findIndex(machine => machine.id === m.machine_id);
        if (idx !== -1) {
          machinesDisplay.value[idx] = {
            ...machinesDisplay.value[idx],
            ...m,
            status: m.status || 'working'
          };
        }
      }

      // Kết nối WebSocket
      socket = io('http://localhost:3001');
      socket.on('machine-data', data => {
        const idx = machinesDisplay.value.findIndex(m => m.id === data.machine_id);
        if (idx !== -1) {
          machinesDisplay.value[idx] = {
            ...machinesDisplay.value[idx],
            ...data,
            status: data.status || 'working'
          };
        }
      });
    });

    onBeforeUnmount(() => {
      if (socket) socket.disconnect();
    });

    const formatTime = (isoStr) => isoStr ? new Date(isoStr).toLocaleTimeString() : '...';

    const getStatusText = (status) => {
      switch (status) {
        case 'working': return '🟢 Đang hoạt động';
        case 'idle': return '🟡 Nghỉ';
        case 'completed': return '✅ Hoàn tất';
        case 'error': return '🔴 Lỗi';
        default: return '🟡 Nghỉ';
      }
    };

    const getProgress = (machine) => {
      const done = Number(machine.quantity_done || 0);
      const total = Number(machine.quantity_total || 0);
      return total ? Math.min(100, Math.floor((done / total) * 100)) : 0;
    };

    return {
      machines: machinesDisplay,
      formatTime,
      getStatusText,
      getProgress
    };
  }
};
</script>

<style scoped>
.progress-bar {
  background-color: #f3f3f3;
  border-radius: 4px;
  height: 20px;
  width: 100%;
  overflow: hidden;
}
.progress-inner {
  height: 100%;
  background-color: #3b82f6; /* blue-500 */
  color: white;
  text-align: center;
  white-space: nowrap;
  font-weight: bold;
  font-size: 0.9rem;
  transition: width 0.5s ease-in-out;
}
</style>
