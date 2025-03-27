<template>
    <div class="mt-3">
      <h5>🔢 Danh sách Giá trị Thuộc tính</h5>
      <div v-for="spec in specsByProduct" :key="spec.id" class="mb-4">
        <h6>🧩 {{ spec.name }} ({{ spec.id }})</h6>

        <div v-for="attr in attributesBySpec[spec.id] || []" :key="attr.id" class="ms-3 mb-3">
          <div class="d-flex justify-content-between align-items-center">
            <strong>🔸 {{ attr.name }} ({{ attr.attribute_type }})</strong>
            <button class="btn btn-success btn-sm" @click="openAddModal(attr.id)">➕ Thêm giá trị</button>
          </div>

          <table class="table table-bordered table-sm mt-2">
            <thead>
              <tr>
                <th>ID</th>
                <th>Giá trị</th>
                <th>Hành động</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="val in valuesByAttribute[attr.id] || []" :key="val.id">
                <td>{{ val.id }}</td>
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
      </div>

      <!-- Modal -->
      <div v-if="showModal" class="modal-overlay">
        <div class="modal-content">
          <h5>{{ isEditing ? '✏️ Sửa' : '➕ Thêm' }} Giá trị</h5>
          <form @submit.prevent="submitForm">
            <div class="mb-2">
              <label>Thuộc tính ID</label>
              <input v-model="form.spec_attribute_id" class="form-control" readonly />
            </div>
            <div class="mb-2">
              <label>Giá trị số</label>
              <input v-model.number="form.number_value" class="form-control" />
            </div>
            <div class="mb-2">
              <label>Giá trị văn bản</label>
              <input v-model="form.text_value" class="form-control" />
            </div>
            <div class="mb-2">
              <label>Giá trị boolean</label>
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
        specs: state => state.specs.specs,
        attributes: state => state.specAttributes.specAttributes,
        values: state => state.specAttributeValues.specAttributeValues
      }),
      specsByProduct() {
        return this.specs.filter(s => s.product_id === this.productId);
      },
      attributesBySpec() {
        const grouped = {};
        this.attributes.forEach(attr => {
          if (!grouped[attr.spec_id]) grouped[attr.spec_id] = [];
          grouped[attr.spec_id].push(attr);
        });
        return grouped;
      },
      valuesByAttribute() {
        const grouped = {};
        this.values.forEach(val => {
          if (!grouped[val.spec_attribute_id]) grouped[val.spec_attribute_id] = [];
          grouped[val.spec_attribute_id].push(val);
        });
        return grouped;
      }
    },
    methods: {
      ...mapActions('specs', ['fetchSpecs']),
      ...mapActions('specAttributes', ['fetchSpecAttributes']),
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
      openAddModal(attributeId) {
        this.isEditing = false;
        this.form = {
          id: '',
          spec_attribute_id: attributeId,
          number_value: null,
          text_value: '',
          boolean_value: null
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
