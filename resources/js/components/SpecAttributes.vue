<template>
  <div class="mt-3">
    <div v-for="spec in specsByProduct" :key="spec.id" class="mb-4">
      <div class="d-flex justify-content-between align-items-center">
        <h6>🧩 {{ spec.name }} ({{ spec.id }})</h6>
        <button class="btn btn-success btn-sm" @click="openAddModal(spec.id)">➕ Thêm giá trị</button>
      </div>

      <table class="table table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Thuộc tính</th>
            <th>Giá trị</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="attr in attributesBySpec[spec.id] || []" :key="attr.id">
            <td>{{ attr.id }}</td>
            <td>{{ attr.name }}</td>
            <td>
              <span v-if="latestValue(attr.id)">
                <span v-if="latestValue(attr.id).number_value !== null">{{ latestValue(attr.id).number_value }}</span>
                <span v-else-if="latestValue(attr.id).text_value">{{ latestValue(attr.id).text_value }}</span>
                <span v-else-if="latestValue(attr.id).boolean_value !== null">
                  {{ latestValue(attr.id).boolean_value ? '✅ Có' : '❌ Không' }}
                </span>
              </span>
              <span v-else class="text-muted">Chưa có giá trị</span>
            </td>
            <td>
              <button class="btn btn-sm btn-primary me-2" @click="editItem(latestValue(attr.id))" :disabled="!latestValue(attr.id)">✏️</button>
              <button class="btn btn-sm btn-danger" @click="deleteItem(latestValue(attr.id).id)" :disabled="!latestValue(attr.id)">🗑️</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay">
      <div class="modal-content">
        <h5>{{ isEditing ? '✏️ Sửa' : '➕ Thêm' }} Giá trị Thuộc tính</h5>
        <form @submit.prevent="submitForm">
          <div class="mb-2">
            <label>Chọn thuộc tính</label>
            <select v-model="form.spec_attribute_id" class="form-control">
              <option disabled value="">-- Chọn --</option>
              <option v-for="a in attributesBySpec[selectedSpecId]" :key="a.id" :value="a.id">
                {{ a.name }} ({{ a.attribute_type }})
              </option>
              <option value="new">➕ Tạo thuộc tính mới</option>
            </select>
          </div>

          <div v-if="form.spec_attribute_id === 'new'" class="border rounded p-2 bg-light mb-2">
            <div class="mb-2">
              <label>ID</label>
              <input v-model="newAttribute.id" class="form-control" required />
            </div>
            <div class="mb-2">
              <label>Spec ID</label>
              <input v-model="newAttribute.spec_id" class="form-control" required />
            </div>
            <div class="mb-2">
              <label>Tên thuộc tính mới</label>
              <input v-model="newAttribute.name" class="form-control" required />
            </div>
            <div class="mb-2">
              <label>Loại</label>
              <select v-model="newAttribute.attribute_type" class="form-control">
                <option value="number">Số</option>
                <option value="text">Văn bản</option>
                <option value="boolean">Đúng/Sai</option>
              </select>
            </div>
          </div>

          <!-- Nhập giá trị -->
          <div class="mb-2">
            <label>Giá trị số</label>
            <input v-model.number="form.number_value" type="number" class="form-control" />
          </div>
          <div class="mb-2">
            <label>Giá trị văn bản</label>
            <input v-model="form.text_value" class="form-control" />
          </div>
          <div class="mb-2">
            <label>Giá trị Boolean</label>
            <select v-model="form.boolean_value" class="form-control">
              <option :value="null">-- Không chọn --</option>
              <option :value="true">✅ Có</option>
              <option :value="false">❌ Không</option>
            </select>
          </div>

          <button class="btn btn-success">{{ isEditing ? 'Cập nhật' : 'Thêm' }}</button>
          <button class="btn btn-secondary ms-2" type="button" @click="closeModal">Huỷ</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex';
import '@/assets/modal.css';

export default {
  props: ['productId'],
  data() {
    return {
      form: {
        id: '',
        spec_attribute_id: '',
        number_value: null,
        text_value: '',
        boolean_value: null
      },
      selectedSpecId: null,
      newAttribute: {
        id: '',
        spec_id: '',
        name: '',
        attribute_type: 'text'
      },
      showModal: false,
      isEditing: false
    };
  },
  computed: {
    ...mapState({
      specs: state => state.specs.specs,
      attributes: state => state.specAttributes.specAttributes,
      values: state => state.specAttributeValues.specAttributeValues
    }),
    specsByProduct() {
      return this.specs.filter(s => s.product_id === this.productId);
    },
    attributesBySpec() {
      const map = {};
      this.attributes.forEach(attr => {
        if (!map[attr.spec_id]) map[attr.spec_id] = [];
        map[attr.spec_id].push(attr);
      });
      return map;
    },
    valuesByAttribute() {
      const map = {};
      this.values.forEach(val => {
        if (!map[val.spec_attribute_id]) map[val.spec_attribute_id] = [];
        map[val.spec_attribute_id].push(val);
      });
      return map;
    }
  },
  methods: {
    ...mapActions('specs', ['fetchSpecs']),
    ...mapActions('specAttributes', ['fetchSpecAttributes', 'createSpecAttribute']),
    ...mapActions('specAttributeValues', [
      'fetchSpecAttributeValues',
      'createSpecAttributeValue',
      'updateSpecAttributeValue',
      'deleteSpecAttributeValue'
    ]),
    async loadData() {
      await this.fetchSpecs();
      await this.fetchSpecAttributes();
      await this.fetchSpecAttributeValues();
    },
    openAddModal(specId) {
      this.selectedSpecId = specId;
      this.isEditing = false;
      this.form = {
        id: '',
        spec_attribute_id: '',
        number_value: null,
        text_value: '',
        boolean_value: null
      };
      this.newAttribute = {
        id: '',
        spec_id: specId,
        name: '',
        attribute_type: 'text'
      };
      this.showModal = true;
    },
    editItem(val) {
      this.isEditing = true;
      this.form = { ...val };
      this.selectedSpecId = this.getSpecIdFromAttribute(val.spec_attribute_id);
      this.showModal = true;
    },
    getSpecIdFromAttribute(attributeId) {
      const attr = this.attributes.find(a => a.id === attributeId);
      return attr?.spec_id || null;
    },
    closeModal() {
      this.showModal = false;
    },
    async submitForm() {
      if (this.form.spec_attribute_id === 'new') {
        try {
          const newAttr = await this.createSpecAttribute({
            id: this.newAttribute.id,
            spec_id: this.selectedSpecId,
            name: this.newAttribute.name,
            attribute_type: this.newAttribute.attribute_type
          });
          this.form.spec_attribute_id = newAttr.data.id;
        } catch (error) {
          console.error('❌ Lỗi tạo thuộc tính:', error.response?.data || error.message);
          alert('Không thể tạo thuộc tính mới. Vui lòng kiểm tra dữ liệu nhập.');
          return;
        }
      }

      if (this.isEditing) {
        await this.updateSpecAttributeValue(this.form);
      } else {
        await this.createSpecAttributeValue(this.form);
      }

      this.closeModal();
      this.loadData();
    },
    async deleteItem(id) {
      if (confirm('Bạn có chắc chắn muốn xoá giá trị này?')) {
        await this.deleteSpecAttributeValue(id);
        this.loadData();
      }
    },
    latestValue(attributeId) {
      const values = this.valuesByAttribute[attributeId];
      return values?.length ? values[0] : null;
    }
  },
  mounted() {
    this.loadData();
  }
};
</script>
