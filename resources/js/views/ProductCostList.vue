<template>
  <div class="mt-4">
    <h5 class="font-semibold mb-2">Chi phí sản xuất</h5>
    <button class="btn btn-success mb-2" @click="openAddModal">Thêm Chi phí</button>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nguyên vật liệu</th>
          <th>Chung</th>
          <th>Tồn kho</th>
          <th>Vận chuyển</th>
          <th>Hao hụt</th>
          <th>Khấu hao</th>
          <th>Thuê ngoài</th>
          <th>Khác</th>
          <th>Tổng</th>
          <th>Ngày tạo</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in filteredCosts" :key="item.id">
          <td>{{ item.id }}</td>
          <td>{{ item.material_cost }}</td>
          <td>{{ item.overhead_cost }}</td>
          <td>{{ item.inventory_cost }}</td>
          <td>{{ item.transportation_cost }}</td>
          <td>{{ item.wastage_cost }}</td>
          <td>{{ item.depreciation_cost }}</td>
          <td>{{ item.service_outsourcing_cost }}</td>
          <td>{{ item.other_costs }}</td>
          <td>{{ item.total_cost }}</td>
          <td>{{ new Date(item.created_at).toLocaleDateString() }}</td>
          <td>
            <button class="btn btn-sm btn-primary me-1" @click="editItem(item)">Sửa</button>
            <button class="btn btn-sm btn-danger" @click="deleteItem(item.id)">Xoá</button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="showModal" class="modal-overlay">
      <div class="modal-content">
        <h4>{{ isEditing ? 'Cập nhật' : 'Thêm' }} Chi phí</h4>
        <form @submit.prevent="submitForm">
          <input v-model="form.id" placeholder="Mã ID" class="form-control mb-2" :readonly="isEditing" required />
          <input v-model="form.product_id" type="hidden" />
          <input v-model.number="form.material_cost" placeholder="Tự động tính nguyên vật liệu" class="form-control mb-2" readonly />

          <input v-model.number="form.overhead_cost" placeholder="Chi phí chung" class="form-control mb-2" />
          <input v-model.number="form.inventory_cost" placeholder="Chi phí tồn kho" class="form-control mb-2" />
          <input v-model.number="form.transportation_cost" placeholder="Chi phí vận chuyển" class="form-control mb-2" />
          <input v-model.number="form.wastage_cost" placeholder="Chi phí hao hụt" class="form-control mb-2" />
          <input v-model.number="form.depreciation_cost" placeholder="Chi phí khấu hao" class="form-control mb-2" />
          <input v-model.number="form.service_outsourcing_cost" placeholder="Chi phí thuê ngoài" class="form-control mb-2" />
          <input v-model.number="form.other_costs" placeholder="Chi phí khác" class="form-control mb-2" />

          <button type="submit" class="btn btn-success">Lưu</button>
          <button type="button" class="btn btn-secondary ms-2" @click="showModal = false">Huỷ</button>
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
      costs: [],
      showModal: false,
      isEditing: false,
      form: {
        id: '',
        product_id: '',
        material_cost: 0,
        overhead_cost: 0,
        inventory_cost: 0,
        transportation_cost: 0,
        wastage_cost: 0,
        depreciation_cost: 0,
        service_outsourcing_cost: 0,
        other_costs: 0
      }
    };
  },
  computed: {
    filteredCosts() {
      return this.costs.filter(c => c.product_id === this.productId);
    }
  },
  methods: {
    openAddModal() {
      this.form = {
        id: 'COST-' + this.productId,
        product_id: this.productId,
        material_cost: 0,
        overhead_cost: 0,
        inventory_cost: 0,
        transportation_cost: 0,
        wastage_cost: 0,
        depreciation_cost: 0,
        service_outsourcing_cost: 0,
        other_costs: 0
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
      await axios.delete(`/api/product-costs/${id}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      await this.fetchData();
    },
    async submitForm() {
      if (this.isEditing) {
        await axios.put(`/api/product-costs/${this.form.id}`, this.form, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      } else {
        await axios.post('/api/product-costs', this.form, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      }
      this.showModal = false;
      await this.fetchData();
    },
    async fetchData() {
      const res = await axios.get('/api/product-costs', {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      this.costs = res.data;
    }
  },
  mounted() {
    this.fetchData();
  }
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-content {
  background: white;
  padding: 20px;
  width: 500px;
  border-radius: 8px;
}
</style>
