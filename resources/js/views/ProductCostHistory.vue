<template>
  <div class="mt-4">
    <h5 class="font-semibold mb-2">Lịch sử chi phí sản xuất</h5>
    <button class="btn btn-success mb-2" @click="openAddModal">Thêm Lịch Sử</button>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Năm</th>
          <th>Chi phí cũ</th>
          <th>Chi phí mới</th>
          <th>Lý do</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="h in filteredHistories" :key="`${h.product_id}-${h.year}`">
          <td>{{ h.year }}</td>
          <td>{{ formatCurrency(h.old_total_cost) }}</td>
          <td>{{ formatCurrency(h.total_cost) }}</td>
          <td>{{ h.reason }}</td>
          <td>
            <button class="btn btn-sm btn-danger" @click="deleteHistory(h)">Xoá</button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="showModal" class="modal-overlay">
      <div class="modal-content">
        <h4>Thêm Lịch Sử Chi Phí</h4>
        <form @submit.prevent="submitForm">
          <input v-model.number="form.year" type="number" class="form-control mb-2" placeholder="Năm" required />
          <input v-model.number="form.old_total_cost" type="number" class="form-control mb-2" placeholder="Chi phí cũ" />
          <input v-model.number="form.total_cost" type="number" class="form-control mb-2" placeholder="Chi phí mới" required />
          <input v-model="form.reason" type="text" class="form-control mb-2" placeholder="Lý do" />
          <button type="submit" class="btn btn-success">Lưu</button>
          <button class="btn btn-secondary ms-2" @click="showModal = false">Huỷ</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  props: ['productId'],
  data() {
    return {
      histories: [],
      showModal: false,
      form: {
        product_id: '',
        year: new Date().getFullYear(),
        old_total_cost: 0,
        total_cost: 0,
        reason: ''
      }
    };
  },
  computed: {
    filteredHistories() {
      return this.histories.filter(h => h.product_id === this.productId);
    }
  },
  methods: {
    formatCurrency(val) {
      return Number(val).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' });
    },
    openAddModal() {
      this.form = {
        product_id: this.productId,
        year: new Date().getFullYear(),
        old_total_cost: 0,
        total_cost: 0,
        reason: ''
      };
      this.showModal = true;
    },
    async submitForm() {
      this.form.product_id = this.productId;
      await axios.post('/api/product-cost-histories', this.form, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      this.showModal = false;
      this.fetchData();
    },
    async deleteHistory(h) {
      if (confirm('Xoá lịch sử chi phí này?')) {
        await axios.delete(`/api/product-cost-histories/${h.id}`,{
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
        this.fetchData();
      }
    },
    async fetchData() {
      const res = await axios.get('/api/product-cost-histories', {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      this.histories = res.data;
    }
  },
  mounted() {
    this.fetchData();
  }
};
</script>
