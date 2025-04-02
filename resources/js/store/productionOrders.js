import axios from 'axios';

export default {
  namespaced: true,
  state: {
    productionOrders: []
  },
  mutations: {
    SET_PRODUCTION_ORDERS(state, list) {
      state.productionOrders = list;
    }
  },
  actions: {
    async fetchProductionOrders({ commit }) {
      try {
        const res = await axios.get('/api/production-orders', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('auth_token')}`
          }
        });
        commit('SET_PRODUCTION_ORDERS', res.data);
      } catch (error) {
        console.error("❌ Lỗi khi lấy danh sách kế hoạch sản xuất:", error);
      }
    }
  }
};
