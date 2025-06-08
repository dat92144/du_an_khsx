import axios from 'axios';

const state = {
  orderGantt: { data: [], links: [] },
  productGantt: { data: [], links: [] },
  lotDetailGantt: { data: [], links: [] },
  machineGantt: { data: [], links: [] },
  realtimeMachineStatus: []
};

const mutations = {
  SET_ORDER_GANTT(state, payload) {
    state.orderGantt.data = payload.data;
    state.orderGantt.links = payload.links;
  },
  SET_PRODUCT_GANTT(state, payload) {
    state.productGantt.data = payload.data;
    state.productGantt.links = payload.links;
  },
  SET_LOT_DETAIL(state, payload) {
    state.lotDetailGantt.data = payload.data;
    state.lotDetailGantt.links = payload.links || [];
  },
  SET_MACHINE_GANTT(state, payload) {
    state.machineGantt.data = payload.data;
    state.machineGantt.links = payload.links;
  },
  SET_REALTIME_MACHINE_STATUS(state, payload) {
    state.realtimeMachineStatus = payload;
  },

  // ✅ Thêm 3 mutation update progress:
  SET_ORDER_PROGRESS(state, { order_id, progress }) {
    const task = state.orderGantt.data.find(t => t.id === order_id);
    if (task) {
        Object.assign(task, { progress });
    }
  },

    SET_PRODUCT_PROGRESS(state, { product_id, progress }) {
        const task = state.productGantt.data.find(t => {
            return t.text?.includes(`Sản phẩm ${product_id}`);
        });
        if (task) {
            Object.assign(task, { progress });
        }
    },

    SET_MACHINE_PLAN_PROGRESS(state, { plan_id, progress }) {
        const task = state.machineGantt.data.find(t => t.id === `plan-${plan_id}`);
        if (task) {
            Object.assign(task, { progress });
        }
    }
};

const actions = {
  async fetchOrderGantt({ commit }) {
    const res = await axios.get('/api/gantt/orders', {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    });
    commit('SET_ORDER_GANTT', res.data);
  },

  async fetchProductGantt({ commit }) {
    const res = await axios.get('/api/gantt/product-lot', {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    });
    commit('SET_PRODUCT_GANTT', res.data);
  },

  async fetchLotDetail({ commit }, { product_id, lot_number }) {
    const res = await axios.get(`/api/gantt/lot-detail?product_id=${product_id}&lot=${lot_number}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    });
    commit('SET_LOT_DETAIL', res.data);
  },

  async fetchMachineGantt({ commit }) {
    const res = await axios.get('/api/gantt/machine', {
      headers: { Authorization: `Bearer ${localStorage.getItem("auth_token")}` }
    });
    commit('SET_MACHINE_GANTT', res.data);
  },

  async fetchRealtimeMachineStatus({ commit }) {
    const res = await axios.get('/api/realtime/machine-status', {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    });
    commit('SET_REALTIME_MACHINE_STATUS', res.data);
  }
};

const getters = {
  orderTasks: state => state.orderGantt.data,
  productTasks: state => state.productGantt.data,
  lotTasks: state => state.lotDetailGantt.data,
  machineTasks: state => state.machineGantt.data,
  realtimeMachineStatus: state => state.realtimeMachineStatus
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
};
