import axios from 'axios';

const state = {
  // Gantt theo đơn hàng
  orderGantt: {
    data: [],
    links: []
  },

  // Gantt theo sản phẩm (gom theo lô)
  productGantt: {
    data: [],
    links: []
  },

  // Gantt chi tiết của từng lô khi click
  lotDetailGantt: {
    data: [],
    links: []
  },

  //Gantt theo máy móc
  machineGantt: {
    data: [],
    links: []
  }
};

const mutations = {
  // Order Gantt
  SET_ORDER_GANTT(state, payload) {
    state.orderGantt.data = payload.data;
    state.orderGantt.links = payload.links;
  },

  // Product Gantt (Sản phẩm → Lô)
  SET_PRODUCT_GANTT(state, payload) {
    state.productGantt.data = payload.data;
    state.productGantt.links = payload.links;
  },

  // Gantt chi tiết công đoạn của 1 lô
  SET_LOT_DETAIL(state, payload) {
    state.lotDetailGantt.data = payload.data;
    state.lotDetailGantt.links = payload.links || [];
  },


  SET_MACHINE_GANTT(state, payload) {
    state.machineGantt.data = payload.data;
    state.machineGantt.links = payload.links;
  }
};

const actions = {
  async fetchOrderGantt({ commit }) {
    try {
      const res = await axios.get('/api/gantt/orders', {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      commit('SET_ORDER_GANTT', res.data);
    } catch (err) {
      console.error('❌ Lỗi tải Gantt đơn hàng:', err);
    }
  },

  async fetchProductGantt({ commit }) {
    try {
      const res = await axios.get('/api/gantt/product-lot', {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      commit('SET_PRODUCT_GANTT', res.data);
    } catch (err) {
      console.error('❌ Lỗi tải Gantt sản phẩm:', err);
    }
  },

  async fetchLotDetail({ commit }, { product_id, lot_number }) {
    try {
      const res = await axios.get(`/api/gantt/lot-detail?product_id=${product_id}&lot=${lot_number}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      commit('SET_LOT_DETAIL', res.data);
    } catch (err) {
      console.error('❌ Lỗi tải Gantt chi tiết lô:', err);
    }
  },

  async fetchMachineGantt({ commit }) {
    const res = await axios.get('/api/gantt/machine', {
      headers: { Authorization: `Bearer ${localStorage.getItem("auth_token")}` }
    });
    commit('SET_MACHINE_GANTT', res.data);
  }
};

const getters = {
  orderTasks: state => state.orderGantt.data,
  productTasks: state => state.productGantt.data,
  lotTasks: state => state.lotDetailGantt.data,
  machineTasks: state => state.machineGantt.data
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
};
