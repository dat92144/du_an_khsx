<template>
    <div class="mt-3">
      <h5 class="flex items-center gap-2 text-lg font-semibold mb-3">
        <Package class="w-5 h-5" /> Danh sách Nguyên Vật Liệu / Công Đoạn trong BOM
      </h5>
      <button class="btn btn-success mb-2 flex items-center gap-1" @click="openAddModal">
        <Plus class="w-4 h-4" /> Thêm mới
      </button>

      <table class="table table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Process</th>
            <th>Product</th>
            <th>Input</th>
            <th>Loại Input</th>
            <th>SL Input</th>
            <th>Đơn vị Input</th>
            <th>Output</th>
            <th>Loại Output</th>
            <th>SL Output</th>
            <th>Đơn vị Output</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in bomItems" :key="item.id">
            <td>{{ item.id }}</td>
            <td>{{ item.process_id }}</td>
            <td>{{ item.product_id }}</td>
            <td>{{ item.input_material_id }}</td>
            <td>{{ item.input_material_type }}</td>
            <td>{{ item.quantity_input }}</td>
            <td>{{ item.input_unit_id }}</td>
            <td>{{ item.output_id }}</td>
            <td>{{ item.output_type }}</td>
            <td>{{ item.quantity_output }}</td>
            <td>{{ item.output_unit_id }}</td>
            <td>
              <button class="btn btn-sm btn-primary me-2" @click="editItem(item)">
                <Pencil class="w-4 h-4" />
              </button>
              <button class="btn btn-sm btn-danger" @click="deleteItem(item.id)">
                <Trash2 class="w-4 h-4" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="showModal" class="modal-overlay">
        <div class="modal-content">
          <h5 class="flex items-center gap-2 mb-3">
            <component :is="isEditing ? Pencil : Plus" class="w-5 h-5" />
            {{ isEditing ? 'Sửa BOM Item' : 'Thêm BOM Item' }}
          </h5>
          <form @submit.prevent="submitForm">
            <div class="mb-2">
              <label>Process ID</label>
              <input v-model="form.process_id" class="form-control" required />
            </div>
            <div class="mb-2">
              <label>Product ID</label>
              <input v-model="form.product_id" class="form-control" required />
            </div>
            <div class="mb-2">
              <label>Input ID</label>
              <input v-model="form.input_material_id" class="form-control" required />
            </div>
            <div class="mb-2">
              <label>Loại Input</label>
              <select v-model="form.input_material_type" class="form-control">
                <option value="materials">Nguyên vật liệu</option>
                <option value="semi_finished_products">Bán thành phẩm</option>
              </select>
            </div>
            <div class="mb-2">
              <label>Số lượng Input</label>
              <input type="number" v-model="form.quantity_input" class="form-control" required />
            </div>
            <div class="mb-2">
              <label>Output ID</label>
              <input v-model="form.output_id" class="form-control" required />
            </div>
            <div class="mb-2">
              <label>Loại Output</label>
              <select v-model="form.output_type" class="form-control">
                <option value="products">Sản phẩm</option>
                <option value="semi_finished_products">Bán thành phẩm</option>
              </select>
            </div>
            <div class="mb-2">
              <label>Số lượng Output</label>
              <input type="number" v-model="form.quantity_output" class="form-control" required />
            </div>

            <button class="btn btn-success">{{ isEditing ? 'Cập nhật' : 'Thêm' }}</button>
            <button class="btn btn-secondary ms-2" @click="closeModal" type="button">Huỷ</button>
          </form>
        </div>
      </div>
    </div>
  </template>

  <script>
  import { mapState, mapActions } from 'vuex';
  import '@/assets/modal.css';
  import { Package, Plus, Pencil, Trash2 } from 'lucide-vue-next';

  export default {
    props: ['bomId'],
    components: {
      Package,
      Plus,
      Pencil,
      Trash2
    },
    data() {
      return {
        form: {
          id: '',
          bom_id: '',
          process_id: '',
          product_id: '',
          input_material_id: '',
          input_material_type: 'materials',
          quantity_input: 1,
          input_unit_id: '',
          output_id: '',
          output_type: 'products',
          quantity_output: 1,
          output_unit_id: ''
        },
        showModal: false,
        isEditing: false
      };
    },
    computed: {
      ...mapState('bomItems', ['bomItems'])
    },
    methods: {
      ...mapActions('bomItems', ['fetchBomItems', 'createBomItem', 'updateBomItem', 'deleteBomItem']),
      async loadItems() {
        await this.fetchBomItems(this.bomId);
      },
      openAddModal() {
        this.isEditing = false;
        this.form = {
          id: '',
          input_material_id: '',
          input_material_type: 'materials',
          quantity_input: 1,
          output_id: '',
          output_type: 'products',
          quantity_output: 1
        };
        this.showModal = true;
      },
      editItem(item) {
        this.isEditing = true;
        this.form = { ...item };
        this.showModal = true;
      },
      closeModal() {
        this.showModal = false;
      },
      async submitForm() {
        this.form.bom_id = this.bomId;
        if (this.isEditing) {
          await this.updateBomItem(this.form);
        } else {
          await this.createBomItem(this.form);
        }
        this.closeModal();
        this.loadItems();
      },
      async deleteItem(id) {
        if (confirm('Bạn có chắc chắn muốn xoá?')) {
          await this.deleteBomItem(id);
          this.loadItems();
        }
      }
    },
    mounted() {
      this.loadItems();
    }
  };
  </script>
