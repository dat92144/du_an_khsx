<template>
    <div class="container mt-4">
        <h2>🏭 Danh sách Nhà Cung Cấp</h2>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Liên Hệ</th>
                    <th>Ngày Tạo</th>
                    <th>Chi Tiết</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="supplier in suppliers" :key="supplier.id">
                    <td>{{ supplier.id }}</td>
                    <td>{{ supplier.name }}</td>
                    <td>{{ supplier.contact_info }}</td>
                    <td>{{ new Date(supplier.created_at).toLocaleDateString() }}</td>
                    <td>
                        <button class="btn btn-info btn-sm" @click="viewPrices(supplier.id)">
                            Xem Giá
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Modal hiển thị bảng giá -->
        <div v-if="selectedSupplier" class="modal-overlay">
            <div class="modal-content">
                <h3 class="mb-3">📦 Bảng Giá Nguyên Vật Liệu - {{ selectedSupplier.id }}</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nguyên Vật Liệu</th>
                            <th>Giá (VNĐ)</th>
                            <th>Đơn Vị</th>
                            <th>Thời gian giao hàng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="price in supplierPrices" :key="price.id">
                            <td>{{ price.material }}</td>
                            <td>{{ price.price_per_unit ? price.price_per_unit.toLocaleString() : 'N/A'  }}</td>
                            <td>{{ price.unit }}</td>
                            <td>{{ price.delivery_time }} ngày</td>
                        </tr>
                    </tbody>
                </table>
                <button class="modal-close" @click="selectedSupplier = null">Đóng</button>
            </div>
        </div>
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex';
import '@/assets/modal.css'; // Import CSS dùng chung

export default {
    data() {
        return {
            selectedSupplier: null,
            supplierPrices: []
        };
    },
    computed: {
        ...mapState('suppliers', {
            suppliers: state => state.suppliers
        })
    },
    methods: {
        ...mapActions('suppliers', ['fetchSuppliers', 'fetchSupplierPrices']),
        async viewPrices(supplierId) {
            this.selectedSupplier = this.suppliers.find(s => s.id === supplierId);
            this.supplierPrices = await this.fetchSupplierPrices(supplierId);
        }
    },
    mounted() {
        this.fetchSuppliers();
    }
};
</script>
