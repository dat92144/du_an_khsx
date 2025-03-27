<template>
    <div class="container mt-4">
      <h2>🚲 Danh sách Sản Phẩm</h2>
  
      <!-- Nút Thêm -->
      <button class="btn btn-success mb-3" @click="openAddModal">➕ Thêm Sản Phẩm</button>
  
      <!-- Bảng sản phẩm -->
      <table class="table table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Tên</th>
            <th>Mô tả</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" :key="product.id">
            <td>{{ product.id }}</td>
            <td>{{ product.name }}</td>
            <td>{{ product.description }}</td>
            <td>{{ new Date(product.created_at).toLocaleDateString() }}</td>
            <td>
              <button class="btn btn-info btn-sm me-2" @click="toggleDetail(product.id)">👁️ Xem chi tiết</button>
              <button class="btn btn-primary btn-sm me-2" @click="openEditModal(product)">✏️ Sửa</button>
              <button class="btn btn-danger btn-sm" @click="deleteProduct(product.id)">🗑️ Xoá</button>
            </td>
          </tr>
          <!-- Chi tiết sản phẩm -->
          <tr v-if="currentProduct" :key="currentProduct.id + '-detail'">
            <td colspan="5">
                <div class="card p-3">
                <h5>🔗 BOM</h5>
                <BomList :productId="currentProduct.id" />

                <hr />
                <h5>🔧 Thông số kỹ thuật</h5>
                <SpecList :productId="currentProduct.id" />

                <hr />
                <h5>📐 Thuộc tính & Giá trị</h5>
                <SpecAttributes :productId="currentProduct.id" />
                </div>
            </td>
            </tr>
        </tbody>
      </table>
  
      <!-- Modal Thêm/Sửa -->
      <div v-if="showModal" class="modal-overlay">
        <div class="modal-content">
          <h3 class="mb-3">{{ isEditing ? '✏️ Sửa Sản Phẩm' : '➕ Thêm Sản Phẩm' }}</h3>
          <form @submit.prevent="submitForm">
            <div class="mb-3">
              <label class="form-label">Mã sản phẩm (ID)</label>
              <input v-model="form.id" class="form-control" required :readonly="isEditing" />
            </div>
            <div class="mb-3">
              <label class="form-label">Tên sản phẩm</label>
              <input v-model="form.name" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Mô tả</label>
              <textarea v-model="form.description" class="form-control"></textarea>
            </div>
            <button class="btn btn-success" type="submit">
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
  import BomList from '../components/BomList.vue';
  import SpecList from '../components/SpecList.vue';
  import SpecAttributes from '../components/SpecAttributes.vue';
  import '@/assets/modal.css';
  
  export default {
    components: { BomList, SpecList, SpecAttributes },
    data() {
      return {
        showModal: false,
        isEditing: false,
        detailProductId: null,
        form: {
          id: '',
          name: '',
          description: ''
        }
      };
    },
    computed: {
      ...mapState('products', ['products']),
      currentProduct() {
            return this.products.find(p => p.id === this.detailProductId) || null;
        }
    },
    methods: {
      ...mapActions('products', ['fetchProducts', 'createProduct', 'updateProduct', 'deleteProductById']),
      openAddModal() {
        this.form = { id: '', name: '', description: '' };
        this.isEditing = false;
        this.showModal = true;
      },
      openEditModal(product) {
        this.form = { ...product };
        this.isEditing = true;
        this.showModal = true;
      },
      closeModal() {
        this.showModal = false;
      },
      async submitForm() {
        if (this.isEditing) {
          await this.updateProduct(this.form);
        } else {
          await this.createProduct(this.form);
        }
        this.closeModal();
        this.fetchProducts();
      },
      async deleteProduct(id) {
        if (confirm('Bạn có chắc chắn muốn xoá sản phẩm này?')) {
          await this.deleteProductById(id);
          this.fetchProducts();
        }
      },
      toggleDetail(id) {
        this.detailProductId = this.detailProductId === id ? null : id;
      }
    },
    mounted() {
      this.fetchProducts();
    }
  };
  </script>
  