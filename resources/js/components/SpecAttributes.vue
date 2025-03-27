<template>
    <div class="mt-3">
      <div v-for="spec in specsByProduct" :key="spec.id" class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
          <h6>🧩 {{ spec.name }} ({{ spec.id }})</h6>
          <button class="btn btn-success btn-sm" @click="openAddModal(spec.id)">➕ Thêm giá trị</button>
        </div>

        <table class="table table-bordered table-sm">
          <thead>
            <tr>
              <th>ID</th>
              <th>Thuộc tính</th>
              <th>Giá trị</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="val in valuesBySpec[spec.id] || []" :key="val.id">
              <td>{{ val.id }}</td>
              <td>{{ val.attributeName }}</td>
              <td>
                <span v-if="val.number_value !== null">{{ val.number_value }}</span>
                <span v-else-if="val.text_value">{{ val.text_value }}</span>
                <span v-else-if="val.boolean_value !== null">{{ val.boolean_value ? '✅ Có' : '❌ Không' }}</span>
              </td>
              <td>
                <button class="btn btn-sm btn-primary me-2" @click="editItem(val)">✏️</button>
                <button class="btn btn-sm btn-danger" @click="deleteItem(val.id)">🗑️</button>
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
              <label>Spec Attribute ID</label>
              <input v-model="form.spec_attribute_id" class="form-control" required readonly />
            </div>
            <div class="mb-2">
              <label>Giá trị Số</label>
              <input v-model.number="form.number_value" class="form-control" type="number" />
            </div>
            <div class="mb-2">
              <label>Giá trị Văn bản</label>
              <input v-model="form.text_value" class="form-control" />
            </div>
            <div class="mb-2">
              <label>Giá trị Boolean</label>
              <select v-model="form.boolean_value" class="form-control">
                <option :value="null">-- Không chọn --</option>
                <option :value="true">Có</option>
                <option :value="false">Không</option>
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
        showModal: false,
        isEditing: false
      };
    },
    computed: {
      ...mapState({
        values: state => state.specAttributeValues.specAttributeValues,
        attributes: state => state.specAttributes.specAttributes,
        specs: state => state.specs.specs
      }),
      specsByProduct() {
        return this.specs.filter(s => s.product_id === this.productId);
      },
      valuesBySpec() {
        const map = {};
        this.values.forEach(val => {
          const attr = this.attributes.find(a => a.id === val.spec_attribute_id);
          if (attr && attr.spec_id) {
            const specId = attr.spec_id;
            if (!map[specId]) map[specId] = [];
            map[specId].push({ ...val, attributeName: attr.name });
          }
        });
        return map;
      }
    },
    methods: {
      ...mapActions('specAttributeValues', ['fetchSpecAttributeValues', 'createSpecAttributeValue', 'updateSpecAttributeValue', 'deleteSpecAttributeValue']),
      ...mapActions('specAttributes', ['fetchSpecAttributes']),
      ...mapActions('specs', ['fetchSpecs']),
      async loadData() {
        await this.fetchSpecs();
        await this.fetchSpecAttributes();
        await this.fetchSpecAttributeValues();
      },
      openAddModal(specId) {
        const attr = this.attributes.find(a => a.spec_id === specId);
        if (!attr) return alert('Không có thuộc tính nào để gán giá trị!');
        this.isEditing = false;
        this.form = {
          id: '',
          spec_attribute_id: attr.id,
          number_value: null,
          text_value: '',
          boolean_value: null
        };
        this.showModal = true;
      },
      editItem(val) {
        this.isEditing = true;
        this.form = { ...val };
        this.showModal = true;
      },
      closeModal() {
        this.showModal = false;
      },
      async submitForm() {
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
      }
    },
    mounted() {
      this.loadData();
    }
  };
  </script>
