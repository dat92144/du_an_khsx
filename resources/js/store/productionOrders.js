import axios from "axios";

const state = {
  productionOrders: [],
  productionHistories: [],
  productionPlans: []
};

const mutations = {
  SET_PRODUCTION_ORDERS(state, orders) {
    state.productionOrders = orders;
  },
  SET_PRODUCTION_HISTORIES(state, histories) {
    state.productionHistories = histories;
  },
  SET_PRODUCTION_PLANS(state, plans) {
    state.productionPlans = plans;
  }
};

const actions = {
  async fetchProductionOrders({ commit }) {
    try {
      const res = await axios.get("/api/production-orders", {
        headers: { Authorization: `Bearer ${localStorage.getItem("auth_token")}` }
      });
      commit("SET_PRODUCTION_ORDERS", res.data);
    } catch (err) {
      console.error("❌ Lỗi khi lấy danh sách production orders:", err);
    }
  },

  async fetchProductionHistories({ commit }) {
    try {
      const res = await axios.get("/api/production-histories", {
        headers: { Authorization: `Bearer ${localStorage.getItem("auth_token")}` }
      });
      commit("SET_PRODUCTION_HISTORIES", res.data);
    } catch (err) {
      console.error("❌ Lỗi khi lấy lịch sử sản xuất:", err);
    }
  },

  async fetchPlansByOrder({ commit }, orderId) {
    try {
      const res = await axios.get(`/api/production-orders/${orderId}/plans`, {
        headers: { Authorization: `Bearer ${localStorage.getItem("auth_token")}` }
      });
      commit("SET_PRODUCTION_PLANS", res.data);
    } catch (err) {
      console.error("❌ Lỗi khi lấy kế hoạch theo đơn hàng:", err);
    }
  },

  async autoGeneratePlan({ dispatch }) {
    try {
      await axios.post("/api/production-planning", {}, {
        headers: { Authorization: `Bearer ${localStorage.getItem("auth_token")}` }
      });
      // Có thể gọi lại danh sách đơn hàng nếu cần
      await dispatch("fetchProductionOrders");
    } catch (err) {
      console.error("❌ Lỗi khi tự động lập kế hoạch sản xuất:", err);
    }
  },
  async fetchProductionPlans({ commit }) {
    try {
      const res = await axios.get('/api/production-plans', {
        headers: { Authorization: `Bearer ${localStorage.getItem("auth_token")}` }
      });

      const tasks = [];

      // Tạo tập hợp đơn hàng + sản phẩm
      const orders = {};

      res.data.forEach(item => {
        if (!orders[item.order_id]) {
          orders[item.order_id] = {
            id: item.order_id,
            text: `Đơn hàng ${item.order_id}`,
            start_date: item.start_time,
            duration: 1,
            parent: 0
          };
          tasks.push(orders[item.order_id]);
        }

        const productKey = `${item.order_id}-${item.product_id}`;

        if (!orders[productKey]) {
          orders[productKey] = {
            id: productKey,
            text: item.product?.name ?? "Sản phẩm",
            start_date: item.start_time,
            duration: 1,
            parent: item.order_id
          };
          tasks.push(orders[productKey]);
        }

        const start = new Date(item.start_time);
        const end = new Date(item.end_time);
        const duration = Math.max(1, Math.ceil(
          (new Date(item.end_time) - new Date(item.start_time)) / (1000 * 60) // phút
        ));

        tasks.push({
          id: item.plan_id,
          text: item.process?.name ?? "Công đoạn",
          start_date: item.start_time,
          duration: duration,
          parent: productKey
        });
      });

      commit('SET_PRODUCTION_PLANS', {
        data: tasks,
        links: []
      });
    } catch (err) {
      console.error("❌ Lỗi khi lấy production plans:", err);
    }
  }

};

export default {
  namespaced: true,
  state,
  mutations,
  actions
};
