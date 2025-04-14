<template>
    <div class="container mt-4">
      <h2 class="flex items-center gap-2 text-xl font-bold mb-3">
        <Wrench class="w-5 h-5" /> Danh sách Máy Móc
      </h2>

      <button class="btn btn-success mb-3 d-flex align-items-center gap-1" @click="openAddModal">
        <Plus class="w-4 h-4" /> Thêm Máy Móc
      </button>

      <table class="table table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Tên máy</th>
            <th>Mã máy</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="machine in machines" :key="machine.id">
            <td>{{ machine.id }}</td>
            <td>{{ machine.name }}</td>
            <td>{{ machine.description }}</td>
            <td>{{ new Date(machine.created_at).toLocaleDateString() }}</td>
            <td>
              <button class="btn btn-primary btn-sm me-2 d-flex align-items-center gap-1" @click="openEditModal(machine)">
                <Pencil class="w-4 h-4" /> Sửa
              </button>
              <button class="btn btn-danger btn-sm d-flex align-items-center gap-1" @click="deleteItem(machine.id)">
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
            {{ isEditing ? 'Cập nhật Máy Móc' : 'Thêm Máy Móc' }}
          </h3>

          <form @submit.prevent="submitForm">
            <div class="mb-3">
              <label class="form-label">Mã máy</label>
              <input v-model="form.id" class="form-control" required :readonly="!!isEditing" />
            </div>
            <div class="mb-3">
              <label class="form-label">Tên máy</label>
              <input v-model="form.name" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Ghi chú</label>
              <input v-model="form.description" class="form-control" required />
            </div>
            <button type="submit" class="btn btn-success">
              {{ isEditing ? 'Cập nhật' : 'Thêm mới' }}
            </button>
            <button type="button" class="btn btn-secondary ms-2" @click="closeModal">Huỷ</button>
          </form>
        </div>
      </div>
    </div>
  </template>

  <script>
  import { mapState, mapActions } from 'vuex';
  import '@/assets/modal.css';
  import { Wrench, Plus, Pencil, Trash2 } from 'lucide-vue-next';

  export default {
    components: {
      Wrench,
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
      ...mapState('machines', {
        machines: state => state.machines
      })
    },
    methods: {
      ...mapActions('machines', ['fetchMachines', 'createMachine', 'updateMachine', 'deleteMachine']),
      openAddModal() {
        this.form = { id: '', name: '', description: '' };
        this.isEditing = false;
        this.showModal = true;
      },
      openEditModal(machine) {
        this.form = { ...machine };
        this.isEditing = true;
        this.showModal = true;
      },
      closeModal() {
        this.showModal = false;
      },
      async submitForm() {
        if (this.form.id && this.isEditing) {
          await this.updateMachine(this.form);
        } else {
          await this.createMachine(this.form);
        }
        this.closeModal();
        this.fetchMachines();
      },
      async deleteItem(id) {
        if (confirm('Bạn có chắc chắn muốn xoá máy này?')) {
          await this.deleteMachine(id);
          this.fetchMachines();
        }
      }
    },
    mounted() {
      this.fetchMachines();
    }
  };
  </script>
