<template>
    <div class="container mt-4">
      <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
        <Package class="w-6 h-6" /> Quản lý đơn đặt hàng
      </h2>

      <button class="bg-green-600 text-white px-4 py-2 rounded mb-4 flex items-center gap-2" @click="openAddModal">
        <Plus class="w-4 h-4" /> Thêm đơn hàng
      </button>

      <table class="table-auto w-full border">
        <thead>
          <tr class="bg-gray-200">
            <th class="px-4 py-2">Mã đơn</th>
            <th class="px-4 py-2">Khách hàng</th>
            <th class="px-4 py-2">Ngày đặt</th>
            <th class="px-4 py-2">Ngày giao hàng</th>
            <th class="px-4 py-2">Trạng thái</th>
            <th class="px-4 py-2">Hành động</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="order in orders" :key="order.id" class="border-t">
            <td class="px-4 py-2">{{ order.id }}</td>
            <td class="px-4 py-2">{{ order.customer_id }}</td>
            <td class="px-4 py-2">{{ formatDate(order.order_date) }}</td>
            <td class="px-4 py-2">{{ formatDate(order.delivery_date) }}</td>
            <td class="px-4 py-2">
              <span :class="order.status === 'approved' ? 'text-green-600' : 'text-yellow-600'">
                {{ getStatusText(order.status) }}
              </span>
            </td>
            <td class="px-4 py-2 flex flex-wrap gap-2">
              <button @click="toggleDetail(order.id)" class="bg-blue-500 text-white px-3 py-1 rounded flex items-center gap-1">
                <Eye class="w-4 h-4" /> Chi tiết
              </button>
              <button @click="openEditModal(order)" class="bg-indigo-500 text-white px-3 py-1 rounded flex items-center gap-1">
                <Pencil class="w-4 h-4" /> Sửa
              </button>
              <button @click="handleDelete(order.id)" class="bg-red-500 text-white px-3 py-1 rounded flex items-center gap-1">
                <Trash2 class="w-4 h-4" /> Xoá
              </button>
              <button
                v-if="order.status !== 'approved'"
                @click="startProduction(order.id)"
                class="bg-green-500 text-white px-3 py-1 rounded flex items-center gap-1"
              >
                <Factory class="w-4 h-4" /> Tiến hành SX
              </button>
            </td>
          </tr>

          <tr v-if="selectedOrderDetail" class="bg-gray-50">
            <td colspan="5" class="p-4">
              <h4 class="font-semibold mb-2 flex items-center gap-2">
                <ClipboardList class="w-5 h-5" /> Chi tiết đơn hàng
              </h4>
              <table class="table w-full border">
                <thead>
                  <tr class="bg-gray-100">
                    <th class="p-2">Mã chi tiết</th>
                    <th class="p-2">Loại</th>
                    <th class="p-2">Tên sản phẩm</th>
                    <th class="p-2">Số lượng</th>
                    <th class="p-2">Đơn vị</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="detail in selectedOrderDetail.details" :key="detail.id">
                    <td class="p-2">{{ detail.id }}</td>
                    <td class="p-2">{{ detail.product_type === 'product' ? 'Thành phẩm' : 'Bán thành phẩm' }}</td>
                    <td class="p-2">
                      {{ getProductName(detail.product_id || detail.semi_finished_product_id, detail.product_type) }}
                    </td>
                    <td class="p-2">{{ detail.quantity_product }}</td>
                    <td class="p-2">{{ getUnitName(detail.unit_id) }}</td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
        </tbody>
      </table>

      <OrderForm
        v-if="showModal"
        :order="orderFormData"
        :isEditing="isEditing"
        @close="closeModal"
        @save="saveOrder"
      />
    </div>
  </template>

  <script>
  import { mapState, mapActions } from 'vuex';
  import OrderForm from '../components/OrderForm.vue';
  import {
    Package,
    Plus,
    Eye,
    Pencil,
    Trash2,
    Factory,
    ClipboardList
  } from 'lucide-vue-next';

  export default {
    components: {
      OrderForm,
      Package,
      Plus,
      Eye,
      Pencil,
      Trash2,
      Factory,
      ClipboardList
    },
    data() {
      return {
        showModal: false,
        isEditing: false,
        detailOrderId: null,
        orderFormData: null
      };
    },
    computed: {
      ...mapState('orders', ['orders']),
      ...mapState({
        products: state => state.products.products,
        semiProducts: state => state.products.semiProducts,
        units: state => state.units.units
      }),
      selectedOrderDetail() {
        return this.orders.find(o => o.id === this.detailOrderId) || null;
      }
    },
    methods: {
      ...mapActions('orders', ['fetchOrders', 'createOrder', 'updateOrder', 'deleteOrder', 'produceOrder']),
      ...mapActions('products', ['fetchProducts', 'fetchSemiProducts']),
      ...mapActions('units', ['fetchUnits']),


      formatDate(dateStr) {
        return new Date(dateStr).toLocaleDateString();
      },
      getStatusText(status) {
        return status === 'approved' ? 'Đã duyệt' : 'Chờ xử lý';
      },
      toggleDetail(id) {
        this.detailOrderId = this.detailOrderId === id ? null : id;
      },
      openAddModal() {
        this.orderFormData = {
          id: '',
          customer_id: '',
          order_date: '',
         // delivery_date: '',
          details: []
        };
        this.isEditing = false;
        this.showModal = true;
      },
      openEditModal(order) {
        this.orderFormData = JSON.parse(JSON.stringify(order));
        this.isEditing = true;
        this.showModal = true;
      },
      closeModal() {
        this.showModal = false;
        this.orderFormData = null;
      },
      async saveOrder(orderData) {
        if (this.isEditing) {
          await this.updateOrder(orderData);
        } else {
          await this.createOrder(orderData);
        }
        this.closeModal();
        this.fetchOrders();
      },
      getUnitName(unitId) {
        const unit = this.units.find(u => u.id === unitId);
        return unit ? unit.name : unitId;
      },
      async handleDelete(id) {
        console.log('👉 Bấm xoá đơn hàng:', id); // Thêm dòng này
        if (confirm('Bạn có chắc chắn muốn xoá đơn hàng này?')) {
          await this.deleteOrder(id);
          this.fetchOrders();
        }
      },
      async startProduction(orderId) {
        await this.produceOrder(orderId);
        this.fetchOrders();
      },
      getProductName(id, type) {
        const list = type === 'product' ? this.products : this.semiProducts || [];
        const item = list.find(p => p.id === id);
        return item ? `${item.name} (${item.id})` : `[${id}]`;
      }
    },
    mounted() {
      this.fetchOrders();
      this.fetchProducts();
      this.fetchSemiProducts();
      this.fetchUnits();
    }
  };
  </script>
