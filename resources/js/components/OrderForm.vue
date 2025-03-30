<template>
    <div class="modal-overlay">
      <div class="modal-content">
        <h3 class="mb-3 font-bold">
          {{ isEditing ? '✏️ Sửa đơn hàng' : '➕ Thêm đơn hàng' }}
        </h3>
  
        <form @submit.prevent="submitForm">
          <div class="mb-3">
            <label class="form-label">Mã đơn hàng</label>
            <input v-model="form.id" class="form-control" :readonly="isEditing" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Mã khách hàng</label>
            <input v-model="form.customer_id" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Ngày đặt</label>
            <input type="date" v-model="form.order_date" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Ngày giao</label>
            <input type="date" v-model="form.delivery_date" class="form-control" required />
          </div>
  
          <h4 class="mt-4 font-semibold">🛒 Danh sách sản phẩm</h4>
  
          <div v-for="(detail, index) in form.details" :key="index" class="border p-3 mb-2 rounded">
            <div class="mb-2">
              <label class="form-label">Mã chi tiết</label>
              <input v-model="detail.id" class="form-control" required />
            </div>
            <div class="mb-2">
              <label class="form-label">Mã đơn hàng</label>
              <input v-model="form.id" class="form-control" :readonly="isEditing" required />
            </div>
            <div class="mb-2">
              <label class="form-label">Sản phẩm</label>
              <select v-model="detail.product_id" class="form-control" required>
                <option value="">-- Chọn sản phẩm --</option>
                <option v-for="product in products" :key="product.id" :value="product.id">
                  {{ product.name }} ({{ product.id }})
                </option>
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label">Loại sản phẩm</label>
              <select v-model="detail.product_type" class="form-control" required>
                <option value="">-- Chọn loại sản phẩm --</option>
                <option value="product">Thành phẩm</option>
                <option value="semi_finished_product">Bán thành phẩm</option>
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label">Số lượng</label>
              <input type="number" min="1" v-model="detail.quantity_product" class="form-control" required />
            </div>
            <div class="mb-2">
              <label class="form-label">Đơn vị</label>
              <select v-model="detail.unit_id" class="form-control" required>
                <option value="">-- Chọn đơn vị --</option>
                <option v-for="unit in units" :key="unit.id" :value="unit.id">
                  {{ unit.name }}
                </option>
              </select>
            </div>
            <button type="button" class="btn btn-danger btn-sm mt-2" @click="removeDetail(index)">🗑️ Xoá</button>
          </div>
  
          <button type="button" class="btn btn-secondary mt-2" @click="addDetail">
            ➕ Thêm sản phẩm
          </button>
  
          <div class="mt-4">
            <button class="btn btn-success" type="submit">💾 Lưu đơn hàng</button>
            <button class="btn btn-secondary ms-2" type="button" @click="$emit('close')">❌ Huỷ</button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  import { mapState, mapActions } from 'vuex'
  
  export default {
    props: {
      order: Object,
      isEditing: Boolean
    },
    emits: ['save', 'close'],
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
        products: state => state.products.products, // danh sách sản phẩm từ module products
        units: state => state.units.units // danh sách đơn vị từ module units
      })
    },
    watch: {
        order: {
            immediate: true,
            handler(newOrder) {
            if (newOrder) {
                const copy = JSON.parse(JSON.stringify(newOrder));

                // 🔧 Chuẩn hoá định dạng ngày
                const formatDate = (dateStr) => {
                if (!dateStr) return '';
                return dateStr.length >= 10 ? dateStr.slice(0, 10) : dateStr;
                };

                copy.order_date = formatDate(copy.order_date);
                copy.delivery_date = formatDate(copy.delivery_date);
                copy.created_at = formatDate(copy.created_at);
                copy.updated_at = formatDate(copy.updated_at);

                this.form = copy;
                }else{
                    this.resetForm();
                }
            }
        }
    },
    methods: {
      ...mapActions({
        fetchProducts: 'products/fetchProducts',
        fetchUnits: 'units/fetchUnits'
      }),
      addDetail() {
        this.form.details.push({
          id: '',
          order_id: '',
          product_id: '',
          product_type: '',
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
            order_id: this.form.id
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
      this.fetchUnits();
    }
  };
  </script>
  