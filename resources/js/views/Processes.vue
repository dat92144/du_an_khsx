<template>
    <div class="container mt-4">
      <h2 class="text-xl font-bold mb-3 flex items-center gap-2">
        <Repeat class="w-5 h-5" /> Danh sách Công Đoạn
      </h2>

      <!-- Nút Thêm -->
      <button class="btn btn-success mb-3 d-flex align-items-center gap-2" @click="openAddModal">
        <Plus class="w-4 h-4" /> Thêm Công Đoạn
      </button>

      <!-- Bảng danh sách -->
      <table class="table table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Tên công đoạn</th>
            <th>Mô tả</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in processes" :key="item.id">
            <td>{{ item.id }}</td>
            <td>{{ item.name }}</td>
            <td>{{ item.description }}</td>
            <td>{{ new Date(item.created_at).toLocaleDateString() }}</td>
            <td>
              <button class="btn btn-primary btn-sm me-2 d-flex align-items-center gap-1" @click="openEditModal(item)">
                <Pencil class="w-4 h-4" /> Sửa
              </button>
              <button class="btn btn-danger btn-sm d-flex align-items-center gap-1" @click="deleteItem(item.id)">
                <Trash2 class="w-4 h-4" /> Xoá
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Modal -->
      <div v-if="showModal" class="modal-overlay">
        <div class="modal-content">
          <h3 class="mb-3 flex items-center gap-2">
            <component :is="isEditing ? Pencil : Plus" class="w-5 h-5" />
            {{ isEditing ? 'Cập nhật Công Đoạn' : 'Thêm Công Đoạn' }}
          </h3>

          <form @submit.prevent="submitForm">
            <div class="mb-3">
              <label class="form-label">Mã công đoạn (ID)</label>
              <input v-model="form.id" class="form-control" required :readonly="isEditing" />
            </div>
            <div class="mb-3">
              <label class="form-label">Tên công đoạn</label>
              <input v-model="form.name" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Mô tả</label>
              <textarea v-model="form.description" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-success">
              {{ isEditing ? 'Cập nhật' : 'Thêm mới' }}
            </button>
            <button class="btn btn-secondary ms-2" type="button" @click="closeModal">Huỷ</button>
          </form>
        </div>
      </div>
    </div>
  </template>

  <script>
  import { mapState, mapActions } from 'vuex';
  import '@/assets/modal.css';
  import { Repeat, Plus, Pencil, Trash2 } from 'lucide-vue-next';

  export default {
    components: {
      Repeat,
      Plus,
      Pencil,
      Trash2
    },
    data() {
      return {
        showModal: false,
        isEditing: false,
        form: {
          id: '',
          name: '',
          description: ''
        }
      };
    },
    computed: {
      ...mapState('processes', {
        processes: state => state.processes
      })
    },
    methods: {
      ...mapActions('processes', ['fetchProcesses', 'createProcess', 'updateProcess', 'deleteProcess']),
      openAddModal() {
        this.form = { id: '', name: '', description: '' };
        this.isEditing = false;
        this.showModal = true;
      },
      openEditModal(item) {
        this.form = { ...item };
        this.isEditing = true;
        this.showModal = true;
      },
      closeModal() {
        this.showModal = false;
      },
      async submitForm() {
        if (this.isEditing) {
          await this.updateProcess(this.form);
        } else {
          await this.createProcess(this.form);
        }
        this.closeModal();
        this.fetchProcesses();
      },
      async deleteItem(id) {
        if (confirm('Bạn có chắc chắn muốn xoá công đoạn này?')) {
          await this.deleteProcess(id);
          this.fetchProcesses();
        }
      }
    },
    mounted() {
      this.fetchProcesses();
    }
  };
  </script>
