<template>
  <div class="modal-overlay">
    <div class="modal-content">
      <h3 class="mb-3 font-bold flex items-center gap-2">
        <component :is="iconComponent" class="w-5 h-5" />
        {{ isEditing ? 'Sửa đơn hàng' : 'Thêm đơn hàng' }}
      </h3>

      <form @submit.prevent="submitForm">
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
          <label>Ngày giao hàng dự kiến</label>
          <input class="form-control bg-gray-100 text-gray-700" :value="form.delivery_date" readonly />
        </div>

        <div v-if="form.delivery_date" class="mt-2 text-sm text-gray-600">
          <span class="font-semibold">Ngày giao hàng dự kiến:</span>
          {{ formatDate(form.delivery_date) }}
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
            <select v-model="detail.product_type" class="form-control" required @change="updateEstimatedDelivery">
              <option disabled value="">-- Chọn loại --</option>
              <option value="product">Thành phẩm</option>
              <option value="semi_finished_product">Bán thành phẩm</option>
            </select>
          </div>
          <div class="mb-2">
            <label>Mã sản phẩm</label>
            <select v-model="detail.product_id" class="form-control" required @change="updateEstimatedDelivery">
              <option value="">-- Chọn sản phẩm --</option>
              <option v-for="item in getProductList(detail.product_type)" :key="item.id" :value="item.id">
                {{ item.name }} ({{ item.id }})
              </option>
            </select>
          </div>
          <div class="mb-2">
            <label>Số lượng</label>
            <input type="number" min="1" v-model.number="detail.quantity_product" class="form-control" required @input="updateEstimatedDelivery" />
          </div>
          <div class="mb-2">
            <label>Đơn vị</label>
            <select v-model="detail.unit_id" class="form-control" required @change="updateEstimatedDelivery">
              <option value="">-- Chọn đơn vị --</option>
              <option v-for="unit in units" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
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
          <br />
          <button class="btn btn-secondary ms-2 flex items-center gap-1" @click="$emit('close')" type="button">
            <X class="w-4 h-4" /> Huỷ
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import {
  Plus,
  Pencil,
  Trash2,
  ShoppingCart,
  Save,
  X
} from 'lucide-vue-next';
import axios from 'axios';

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
    }),
    iconComponent() {
      return this.isEditing ? 'Pencil' : 'Plus';
    }
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
    async updateEstimatedDelivery() {
      if (!this.form.details || this.form.details.length === 0) return;
      try {
        const res = await axios.post('/api/orders/estimate-delivery', {
          details: this.form.details
        }, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('auth_token')}`
          }
        });

        this.form = {
          ...this.form,
          delivery_date: res.data.delivery_date?.slice(0, 10)
        };
      } catch (err) {
        console.error('❌ Không thể ước lượng ngày giao hàng:', err);
      }
    },
    formatDate(dateStr) {
      return new Date(dateStr).toLocaleDateString('vi-VN');
    },
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
      this.updateEstimatedDelivery();
    },
    removeDetail(index) {
      this.form.details.splice(index, 1);
      this.updateEstimatedDelivery();
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
    this.$store.dispatch('products/fetchProducts');
    this.$store.dispatch('products/fetchSemiProducts');
    this.$store.dispatch('units/fetchUnits');
  }
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
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
