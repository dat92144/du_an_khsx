<template>
    <div class="mt-3">
      <button class="btn btn-success mb-2" @click="openAddModal">➕ Thêm</button>

      <table class="table table-bordered table-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>Tên</th>
            <th>Mô tả</th>
            <th>Lead</th>
            <th>Cycle</th>
            <th>Lot</th>
            <th>Máy</th>
            <th>Process</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="spec in specsByProduct" :key="spec.id">
            <td>{{ spec.id }}</td>
            <td>{{ spec.name }}</td>
            <td>{{ spec.description }}</td>
            <td>{{ spec.lead_time }}</td>
            <td>{{ spec.cycle_time }}</td>
            <td>{{ spec.lot_size }}</td>
            <td>{{ spec.machine_id }}</td>
            <td>{{ spec.process_id }}</td>
            <td>
              <button class="btn btn-sm btn-primary me-2" @click="edit(spec)">✏️</button>
              <button class="btn btn-sm btn-danger" @click="remove(spec.id)">🗑️</button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Modal -->
      <div v-if="showModal" class="modal-overlay">
        <div class="modal-content">
          <h5>{{ isEditing ? '✏️ Sửa' : '➕ Thêm' }} Spec</h5>
          <form @submit.prevent="submitForm">
            <input v-if="isEditing" v-model="form.id" type="hidden" />
            <div class="mb-2"><label>Tên</label><input v-model="form.name" class="form-control" /></div>
            <div class="mb-2"><label>Mô tả</label><textarea v-model="form.description" class="form-control" /></div>
            <div class="mb-2"><label>Product ID</label><input v-model="form.product_id" class="form-control" /></div>
            <div class="mb-2"><label>Process ID</label><input v-model="form.process_id" class="form-control" /></div>
            <div class="mb-2"><label>Machine ID</label><input v-model="form.machine_id" class="form-control" /></div>
            <div class="mb-2"><label>Lead Time</label><input v-model="form.lead_time" type="number" class="form-control" /></div>
            <div class="mb-2"><label>Cycle Time</label><input v-model="form.cycle_time" type="number" class="form-control" /></div>
            <div class="mb-2"><label>Lot Size</label><input v-model="form.lot_size" type="number" class="form-control" /></div>
            <button class="btn btn-success">{{ isEditing ? 'Cập nhật' : 'Thêm' }}</button>
            <button class="btn btn-secondary ms-2" @click="showModal = false" type="button">Huỷ</button>
          </form>
        </div>
      </div>
    </div>
  </template>

  <script>
  import { mapState, mapActions } from 'vuex';

  export default {
    props: ['productId'],
    data() {
      return {
        form: {
          id: '',
          name: '',
          description: '',
          product_id: '',
          process_id: '',
          machine_id: '',
          lead_time: 1.5,
          cycle_time: 10,
          lot_size: 100
        },
        showModal: false,
        isEditing: false
      };
    },
    computed: {
      ...mapState('specs', ['specs']),
      specsByProduct() {
        return this.specs.filter(s => s.product_id === this.productId);
      }
    },
    methods: {
      ...mapActions('specs', ['fetchSpecs', 'createSpec', 'updateSpec', 'deleteSpec']),
      openAddModal() {
        this.isEditing = false;
        this.form = {
          id: '',
          name: '',
          description: '',
          product_id: this.productId,
          process_id: '',
          machine_id: '',
          lead_time: 1.5,
          cycle_time: 10,
          lot_size: 100
        };
        this.showModal = true;
      },
      edit(spec) {
        this.isEditing = true;
        this.form = { ...spec };
        this.showModal = true;
      },
      async submitForm() {
        if (this.isEditing) {
          await this.updateSpec(this.form);
        } else {
          await this.createSpec(this.form);
        }
        this.showModal = false;
      },
      async remove(id) {
        if (confirm('Xoá công đoạn này?')) {
          await this.deleteSpec(id);
        }
      }
    },
    mounted() {
      this.fetchSpecs();
    }
  };
  </script>

  <style scoped>
  @import '@/assets/modal.css';
  </style>
