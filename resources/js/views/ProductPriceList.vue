<template>
  <div class="container mt-4">
    <h2 class="text-xl font-bold mb-3">Giá bán sản phẩm</h2>
    <button @click="openAddModal" class="btn btn-success mb-3">Thêm giá bán</button>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Sản phẩm</th>
          <th>Năm</th>
          <th>Chi phí</th>
          <th>% Lợi nhuận</th>
          <th>Giá bán</th>
          <th>Trạng thái</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="p in prices" :key="`${p.product_id}-${p.year}`">
          <td>{{ p.product?.name || p.product_id }}</td>
          <td>{{ p.year }}</td>
          <td>{{ formatCurrency(p.total_cost) }}</td>
          <td>{{ p.expected_profit_percent }}%</td>
          <td>{{ formatCurrency(p.sell_price) }}</td>
          <td>
            <span class="badge" :class="p.is_active ? 'bg-success' : 'bg-secondary'">
              {{ p.is_active ? 'Đang áp dụng' : 'Không áp dụng' }}
            </span>
          </td>
          <td>
            <button class="btn btn-primary btn-sm" @click="editItem(p)">Sửa</button>
            <button class="btn btn-danger btn-sm" @click="deleteItem(p.id)">Xoá</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay">
      <div class="modal-content">
        <h3>{{ isEditing ? 'Cập nhật' : 'Thêm' }} giá bán</h3>
        <form @submit.prevent="submitForm">
          <input v-model="form.id" placeholder="Mã giá" class="form-control mb-2" :readonly="isEditing" required />
          <select v-model="form.product_id" class="form-control mb-2" required>
            <option disabled value="">-- Chọn sản phẩm --</option>
            <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }}</option>
          </select>
          <input v-model.number="form.year" type="number" placeholder="Năm" class="form-control mb-2" required />
          <input v-model.number="form.total_cost" type="number" placeholder="Chi phí" class="form-control mb-2" required />
          <input v-model.number="form.expected_profit_percent" type="number" placeholder="% lợi nhuận" class="form-control mb-2" required />
          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" v-model="form.is_active" id="activeCheck">
            <label class="form-check-label" for="activeCheck">Áp dụng</label>
          </div>
          <button type="submit" class="btn btn-success">Lưu</button>
          <button @click="showModal = false" type="button" class="btn btn-secondary">Huỷ</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  data() {
    return {
      prices: [],
      products: [],
      showModal: false,
      isEditing: false,
      form: {
        id: '',
        product_id: '',
        year: new Date().getFullYear(),
        total_cost: 0,
        expected_profit_percent: 20,
        sell_price: 0,
        is_active: true
      }
    };
  },
  methods: {
    async fetchData() {
      const [priceRes, productRes] = await Promise.all([
        axios.get('/api/product-prices', {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      }),
        axios.get('/api/products', {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      })
      ]);
      this.prices = priceRes.data;
      this.products = productRes.data;
    },
    openAddModal() {
      this.form = {
        id: '',
        product_id: '',
        year: new Date().getFullYear(),
        total_cost: 0,
        expected_profit_percent: 20,
        sell_price: 0,
        is_active: true
      };
      this.isEditing = false;
      this.showModal = true;
    },
    editItem(item) {
      this.form = { ...item };
      this.isEditing = true;
      this.showModal = true;
    },
    async deleteItem(id) {
      if (confirm('Xoá giá bán này?')) {
        await axios.delete(`/api/product-prices/${id}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
        this.fetchData();
      }
    },
    async submitForm() {
      this.form.sell_price = this.form.total_cost * (1 + this.form.expected_profit_percent / 100);
      if (this.isEditing) {
        await axios.put(`/api/product-prices/${this.form.id}`, this.form, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      } else {
        await axios.post('/api/product-prices', this.form, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      }
      this.showModal = false;
      this.fetchData();
    },
    formatCurrency(val) {
      return Number(val).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' });
    }
  },
  mounted() {
    this.fetchData();
  }
};
</script>
