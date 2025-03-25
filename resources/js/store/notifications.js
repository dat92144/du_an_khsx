import axios from "axios";
const state = {
    notificationCount: 0,
    purchaseRequests: []
};
const mutations = {
    SET_NOTIFICATIONS(state, { count, requests }) {
        state.notificationCount = count;
        state.purchaseRequests = requests;
        localStorage.setItem("purchaseRequests", JSON.stringify(requests));
    },
    CLEAR_NOTIFICATIONS(state) {
        state.notificationCount = 0;
    },
    UPDATE_NOTIFICATION_STATUS(state, { id, status }) {
        const request = state.purchaseRequests.find(req => req.id === id);
        if (request) {
            request.status = status;
            localStorage.setItem("purchaseRequests", JSON.stringify(state.purchaseRequests));
        }
    },
    REMOVE_NOTIFICATION(state, id) {
        state.purchaseRequests = state.purchaseRequests.filter(req => req.id !== id);
        state.notificationCount = state.purchaseRequests.filter(req => req.status === "pending").length;
        localStorage.setItem("purchaseRequests", JSON.stringify(state.purchaseRequests)); 
    }
};
const actions = {
    async fetchNotifications({ commit }) {
        try {
            const response = await axios.get("/api/notifications", {
                headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
            });
            commit("SET_NOTIFICATIONS", response.data);
        } catch (error) {
            console.error("Lỗi khi lấy thông báo:", error);
        }
    },
    markAsRead({ commit }) {
        commit("CLEAR_NOTIFICATIONS");
    },
    async approveRequest({ commit }, { id, sendEmail }) {
        try {
            await axios.post(`/api/purchase-requests/${id}/approve`, {send_email: sendEmail}, {
                headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
                "Content-Type": "application/json" }
            });
            commit("UPDATE_NOTIFICATION_STATUS", { id, status: "approved" });
        } catch (error) {
            console.error("Lỗi khi duyệt đề xuất:", error);
        }
    },
    async rejectRequest({ commit }, id) {
        try {
            await axios.post(`/api/purchase-requests/${id}/reject`,{} ,{
                headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
            });
            commit("UPDATE_NOTIFICATION_STATUS", { id, status: "rejected" });
        } catch (error) {
            console.error("Lỗi khi từ chối đề xuất:", error);
        }
    },
    async deleteNotification({ commit }, id) {
        try {
            await axios.delete(`/api/purchase-requests/${id}/delete`, {
                headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
            });
            commit("REMOVE_NOTIFICATION", id);
        } catch (error) {
            console.error("Lỗi khi xóa thông báo:", error);
        }
    }
    
};

export default {
    namespaced: true,
    state,
    mutations,
    actions
};