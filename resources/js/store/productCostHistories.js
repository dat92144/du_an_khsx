import axios from 'axios';

export default {
  namespaced: true,
  state: () => ({
    histories: []
  }),
  mutations: {
    SET_HISTORIES(state, data) {
      state.histories = data;
    }
  },
  actions: {
    async fetch({ commit }) {
      const res = await axios.get('/api/product-cost-histories', {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      commit('SET_HISTORIES', res.data);
    },
    async create({ dispatch }, payload) {
      await axios.post('/api/product-cost-histories', payload, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      dispatch('fetch');
    },
    async delete({ dispatch }, id) {
      await axios.delete(`/api/product-cost-histories/${id}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      dispatch('fetch');
    }
  }
};
