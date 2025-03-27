<template>
    <div class="mt-3">
      <h5>📋 Danh sách BOM của sản phẩm</h5>
      <button class="btn btn-success mb-2" @click="openAddModal">➕ Thêm BOM</button>
  
      <table class="table table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Tên BOM</th>
            <th>Mô tả</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
            <template v-for="bom in boms" :key="bom.id">
                <!-- Dòng BOM chính -->
                <tr>
                <td>{{ bom.id }}</td>
                <td>{{ bom.name }}</td>
                <td>{{ bom.description }}</td>
                <td>
                    <button class="btn btn-sm btn-info me-2"  @click="toggleBomDetail(bom.id)">👁️</button>
                    <button class="btn btn-sm btn-info me-2"  @click="editBom(bom)">✏️</button>
                    <button class="btn btn-sm btn-info me-2"  @click="deleteBomConfirm(bom.id)">🗑️</button>
                </td>
                </tr>

                <!-- Dòng chi tiết BOM (ẩn/hiện) -->
                <tr v-if="selectedBomId === bom.id">
                <td colspan="4">
                    <BomItems :bomId="bom.id" />
                </td>
                </tr>
            </template>
        </tbody>


      </table>
  
      <!-- Modal Thêm/Sửa BOM -->
      <div v-if="showModal" class="modal-overlay">
        <div class="modal-content">
          <h5>{{ isEditing ? '✏️ Sửa BOM' : '➕ Thêm BOM' }}</h5>
          <form @submit.prevent="submitForm">
            <div class="mb-2">
              <label>Mã BOM (ID)</label>
              <input v-model="form.id" class="form-control" :readonly="isEditing" required />
            </div>
            <div class="mb-2">
              <label>Tên BOM</label>
              <input v-model="form.name" class="form-control" required />
            </div>
            <div class="mb-2">
              <label>Mô tả</label>
              <textarea v-model="form.description" class="form-control"></textarea>
            </div>
            <button class="btn btn-success">{{ isEditing ? 'Cập nhật' : 'Thêm mới' }}</button>
            <button class="btn btn-secondary ms-2" @click="closeModal" type="button">Huỷ</button>
          </form>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { mapState, mapActions } from 'vuex';
  import BomItems from './BomItems.vue';
  import '@/assets/modal.css';
  
  export default {
    components: { BomItems },
    props: ['productId'],
    data() {
      return {
        showModal: false,
        isEditing: false,
        selectedBomId: null,
        form: {
          id: '',
          name: '',
          description: '',
          product_id: ''
        }
      };
    },
    computed: {
      ...mapState('boms', ['boms'])
    },
    methods: {
      ...mapActions('boms', ['fetchBoms', 'createBom', 'updateBom', 'deleteBomById']),
      async load() {
        await this.fetchBoms(this.productId);
      },
      openAddModal() {
        this.form = { id: '', name: '', description: '', product_id: this.productId };
        this.isEditing = false;
        this.showModal = true;
      },
      editBom(bom) {
        this.form = { ...bom };
        this.isEditing = true;
        this.showModal = true;
      },
      closeModal() {
        this.showModal = false;
      },
      async submitForm() {
        this.form.product_id = this.productId;
        if (this.isEditing) {
          await this.updateBom(this.form);
        } else {
          await this.createBom(this.form);
        }
        this.closeModal();
        this.load();
      },
      async deleteBomConfirm(id) {
        if (confirm('Bạn có chắc muốn xoá BOM này?')) {
          await this.deleteBomById(id);
          this.load();
        }
      },
      toggleBomDetail(id) {
        this.selectedBomId = this.selectedBomId === id ? null : id;
      }
    },
    mounted() {
      this.load();
    }
  };
  </script>
  