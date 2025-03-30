<template>
    <header class="bg-white shadow-md p-4 flex justify-between items-center border-b border-gray-300 relative">
        <h1 class="text-xl font-semibold">Quản lý Sản Xuất</h1>
        <div class="flex items-center gap-4">
            <div class="relative">
                <button @click="toggleNotifications" class="bg-gray-200 p-2 rounded-lg hover:bg-gray-300 relative">
                    🔔
                    <span v-if="notificationCount > 0"
                          class="absolute top-0 right-0 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                        {{ notificationCount }}
                    </span>
                </button>

                <div v-if="showNotifications" class="absolute right-0 mt-2 bg-white shadow-md rounded-lg w-96 z-10">
                    <div class="p-4 border-b flex justify-between items-center">
                        <h2 class="text-lg font-semibold">Thông báo</h2>
                        <button @click="markAsRead" class="text-sm text-blue-500 hover:underline">Đánh dấu đã đọc</button>
                    </div>
                    <ul>
                        <li v-for="request in purchaseRequests" :key="request.id"
                            class="p-3 border-b hover:bg-gray-100 cursor-pointer flex justify-between items-center">
                            <div @click="openRequestModal(request)">
                                📢 {{ request.material_id }} - {{ request.supplier?.name }} <br>
                                <span :class="getStatusClass(request.status)">
                                    {{ getStatusText(request.status) }}
                                </span>
                            </div>
                            <button
                            v-if="request.status !== 'pending'"
                            @click.stop="handleDelete(request.id)"
                            class="text-red-500 hover:text-red-700"
                            >
                            ❌
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <span class="text-gray-700">👤 Admin</span>
            <button @click="logout" class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600">
                Đăng xuất
            </button>
        </div>

        <!-- ✅ Modal chi tiết đề xuất mua hàng -->
        <div v-if="showRequestModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                <h2 class="text-xl font-semibold mb-4">Chi tiết đề xuất mua hàng</h2>

                <p><strong>Nguyên vật liệu:</strong> {{ selectedRequest.material_id }}</p>
                <p><strong>Nhà cung cấp:</strong> {{ selectedRequest.supplier?.name }}</p>
                <p><strong>Số lượng:</strong> {{ selectedRequest.quantity }}</p>
                <p><strong>Giá:</strong> {{ selectedRequest.price_per_unit }}</p>
                <p><strong>Trạng thái:</strong> 
                    <span :class="getStatusClass(selectedRequest.status)">{{ getStatusText(selectedRequest.status) }}</span>
                </p>

                <div class="mt-4 flex gap-4">
                    <button v-if="selectedRequest.status === 'pending'"
                            @click="handleApprove"
                            class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                        Duyệt
                    </button>
                    <button v-if="selectedRequest.status === 'pending'"
                            @click="handleReject"
                            class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                        Từ chối
                    </button>
                </div>
                <button @click="closeRequestModal" class="mt-4 text-blue-500 hover:underline">Đóng</button>
            </div>
        </div>

        <!-- ✅ Modal xác nhận gửi email -->
        <div v-if="showConfirmEmailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                <h2 class="text-xl font-semibold mb-4">Xác nhận gửi email</h2>
                <p>Bạn có chắc chắn muốn gửi email đến nhà cung cấp {{ selectedRequest?.supplier?.contact_info }} không?</p>
                <div class="mt-4 flex gap-4">
                    <button @click="confirmApproval(true)" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                        Có, gửi email
                    </button>
                    <button @click="confirmApproval(false)" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                        Không, chỉ duyệt đơn
                    </button>
                </div>
            </div>
        </div>

    </header>
</template>

<script>
import { mapState, mapActions } from "vuex";
import axios from "axios"; 

export default {
    data() {
        return {
            showNotifications: false,
            showRequestModal: false,
            showConfirmEmailModal: false,
            selectedRequest: {}, 
            isProcessing: false,
        };
    },
    computed: {
        ...mapState("notifications", ["notificationCount", "purchaseRequests"])
    },
    mounted() {
        this.fetchNotifications();
        setInterval(this.fetchNotifications, 60000);
    },
    methods: {
        ...mapActions("notifications", ["fetchNotifications", "markAsRead", "approveRequest", "rejectRequest", "deleteNotification"]),
        toggleNotifications() {
            this.showNotifications = !this.showNotifications;
        },
        openRequestModal(request) {
            this.selectedRequest = request;
            this.showRequestModal = true;
            this.showConfirmEmailModal = false;
        },
        closeRequestModal() {
            this.showRequestModal = false;
            this.showConfirmEmailModal = false;

            // Đợi Vue cập nhật UI xong rồi mới reset `selectedRequest`
            this.$nextTick(() => {
                this.selectedRequest = null;
            });
        },


        async handleApprove() {
            if (!this.selectedRequest) return;
            this.showConfirmEmailModal = true;
            await this.fetchNotifications();
        },

        async confirmApproval(sendEmail) {
            if (!this.selectedRequest || !this.selectedRequest.id) {
                alert("⚠ Không tìm thấy đề xuất hợp lệ!");
                return;
            }

            try {
                console.log("📌 Gửi yêu cầu duyệt với ID:", this.selectedRequest.id, "Gửi Email:", sendEmail);

                await this.approveRequest({ id: this.selectedRequest.id, sendEmail });

                // Đợi API hoàn tất rồi mới reset modal
                this.closeRequestModal();

                alert("✅ Duyệt đề xuất thành công!");
            } catch (error) {
                console.error("❌ Lỗi khi duyệt đề xuất:", error);
                alert("🚨 Lỗi khi duyệt đề xuất: " + (error.response?.data?.message || "Lỗi không xác định!"));
            }
        },

        async handleDelete(id) {
            console.log("👉 Xoá đề xuất ID:", id);
            await this.deleteNotification(id);
            await this.fetchNotifications();
        },
        async handleReject() {
            if (!this.selectedRequest) return;
            await this.rejectRequest(this.selectedRequest.id);
            this.selectedRequest.status = "rejected"; 
            this.showRequestModal = false;
            await this.fetchNotifications();
        },
        getStatusClass(status) {
            return status === "approved" ? "text-green-500 font-semibold" : 
                   status === "rejected" ? "text-red-500 font-semibold" : 
                   "text-gray-500 font-semibold";
        },
        getStatusText(status) {
            return status === "approved" ? "Đã duyệt" : 
                   status === "rejected" ? "Từ chối" : 
                   "Chờ duyệt";
        },
        logout() {
            localStorage.removeItem("auth_token");
            this.$router.push("/login");
        }
    }
};
</script>
