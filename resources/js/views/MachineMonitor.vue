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
        <tr v-for="m in machines" :key="m.plan_id">
          <td>{{ m.machine_id }}</td>
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
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { io } from 'socket.io-client';
import { Activity } from 'lucide-vue-next';

export default {
  components: { Activity },
  setup() {
    const machines = ref([]);
    let socket;

    onMounted(() => {
      socket = io('http://localhost:3001');

      socket.on('machine-data', data => {
        console.log('📥 Nhận được dữ liệu từ WebSocket:', data);
        const idx = machines.value.findIndex(m => m.plan_id === data.plan_id);
        if (idx !== -1) {
          machines.value[idx] = { ...machines.value[idx], ...data };
        } else {
          machines.value.push(data);
        }
      });
    });

    onBeforeUnmount(() => {
      if (socket) socket.disconnect();
    });

    const formatTime = (isoStr) => {
      if (!isoStr) return '...';
      return new Date(isoStr).toLocaleTimeString();
    };

    const getStatusText = (status) => {
      switch (status) {
        case 'working': return '🟢 Đang hoạt động';
        case 'idle': return '🟡 Đang chờ';
        case 'completed': return '✅ Hoàn tất';
        case 'error': return '🔴 Lỗi';
        default: return '❓ Không xác định';
      }
    };

    const getProgress = (machine) => {
      const done = Number(machine.quantity_done);
      const total = Number(machine.quantity_total);
      if (!total || total === 0) return 0;
      return Math.min(100, Math.floor((done / total) * 100));
    };

    return {
      machines,
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
