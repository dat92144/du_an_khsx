<template>
    <div class="modal-overlay">
      <div class="modal-content">
        <h3 class="mb-3 font-bold flex items-center gap-2">
          <component :is="isEditing ? Pencil : Plus" class="w-5 h-5" />
          {{ isEditing ? 'Sửa đơn hàng' : 'Thêm đơn hàng' }}
        </h3>

        <form @submit.prevent="submitForm">
          <!-- Thông tin đơn hàng -->
          <div class="mb-3">
            <label>Mã đơn hàng</label>
            <input v-model="form.id" class="form-control" :readonly="isEditing" required />
          </div>
          <div class="mb-3">
            <label>Mã khách hàng</label>
            <input v-model="form.customer_id" class="form-control" required />
          </div>
          <div class="mb-3">
            <label>Ngày đặt</label>
            <input type="date" v-model="form.order_date" class="form-control" required />
          </div>
          <div class="mb-3">
            <label>Ngày giao</label>
            <input type="date" v-model="form.delivery_date" class="form-control" required />
          </div>

          <h4 class="mt-4 font-semibold flex items-center gap-2">
            <ShoppingCart class="w-5 h-5" /> Danh sách sản phẩm
          </h4>

          <div v-for="(detail, index) in form.details" :key="index" class="border p-3 mb-3 rounded">
            <div class="mb-2">
              <label>Mã chi tiết</label>
              <input v-model="detail.id" class="form-control" required />
            </div>
            <div class="mb-2">
              <label>Loại sản phẩm</label>
              <select v-model="detail.product_type" class="form-control" required>
                <option disabled value="">-- Chọn loại --</option>
                <option value="product">Thành phẩm</option>
                <option value="semi_finished_product">Bán thành phẩm</option>
              </select>
            </div>
            <div class="mb-2">
              <label>Mã sản phẩm</label>
              <select v-model="detail.product_id" class="form-control" required>
                <option value="">-- Chọn sản phẩm --</option>
                <option
                  v-for="item in getProductList(detail.product_type)"
                  :key="item.id"
                  :value="item.id"
                >
                  {{ item.name }} ({{ item.id }})
                </option>
              </select>
            </div>
            <div class="mb-2">
              <label>Số lượng</label>
              <input type="number" min="1" v-model="detail.quantity_product" class="form-control" required />
            </div>
            <div class="mb-2">
              <label>Đơn vị</label>
              <select v-model="detail.unit_id" class="form-control" required>
                <option value="">-- Chọn đơn vị --</option>
                <option v-for="unit in units" :key="unit.id" :value="unit.id">
                  {{ unit.name }}
                </option>
              </select>
            </div>
            <button class="btn btn-danger mt-2 flex items-center gap-1" @click="removeDetail(index)" type="button">
              <Trash2 class="w-4 h-4" /> Xoá
            </button>
          </div>

          <button class="btn btn-secondary flex items-center gap-1" @click="addDetail" type="button">
            <Plus class="w-4 h-4" /> Thêm sản phẩm
          </button>

          <div class="mt-4">
            <button class="btn btn-success flex items-center gap-1" type="submit">
              <Save class="w-4 h-4" /> Lưu đơn hàng
            </button>
            <br>
            <button class="btn btn-secondary ms-2 flex items-center gap-1" @click="$emit('close')" type="button">
              <X class="w-4 h-4" /> Huỷ
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>

  <script>
  import { mapState, mapActions } from 'vuex'
  import {
    Plus,
    Pencil,
    Trash2,
    ShoppingCart,
    Save,
    X
  } from 'lucide-vue-next'

  export default {
    props: {
      order: Object,
      isEditing: Boolean
    },
    emits: ['save', 'close'],
    components: {
      Plus,
      Pencil,
      Trash2,
      ShoppingCart,
      Save,
      X
    },
    data() {
      return {
        form: {
          id: '',
          customer_id: '',
          order_date: '',
          delivery_date: '',
          details: []
        }
      };
    },
    computed: {
      ...mapState({
        products: state => state.products.products,
        semiProducts: state => state.products.semiProducts,
        units: state => state.units.units
      })
    },
    watch: {
      order: {
        immediate: true,
        handler(newOrder) {
          if (newOrder) {
            const copy = JSON.parse(JSON.stringify(newOrder));
            const formatDate = str => str?.slice(0, 10);
            copy.order_date = formatDate(copy.order_date);
            copy.delivery_date = formatDate(copy.delivery_date);
            this.form = copy;
          } else {
            this.resetForm();
          }
        }
      }
    },
    methods: {
      ...mapActions({
        fetchProducts: 'products/fetchProducts',
        fetchSemiProducts: 'products/fetchSemiProducts',
        fetchUnits: 'units/fetchUnits'
      }),
      getProductList(type) {
        return type === 'product' ? this.products : this.semiProducts || [];
      },
      addDetail() {
        this.form.details.push({
          id: '',
          product_type: '',
          product_id: '',
          quantity_product: 1,
          unit_id: ''
        });
      },
      removeDetail(index) {
        this.form.details.splice(index, 1);
      },
      submitForm() {
        this.form.details = this.form.details.map(detail => ({
          ...detail,
          order_id: this.form.id,
          product_id: detail.product_id || null
        }));
        this.$emit('save', this.form);
      },
      resetForm() {
        this.form = {
          id: '',
          customer_id: '',
          order_date: '',
          delivery_date: '',
          details: []
        };
      }
    },
    mounted() {
      this.fetchProducts();
      this.fetchSemiProducts();
      this.fetchUnits();
    }
  }
  </script>

  <style scoped>
  .modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .modal-content {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    width: 90%;
    max-width: 700px;
  }
  </style>
