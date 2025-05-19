import axios from 'axios';

export default {
  namespaced: true,
  state: () => ({
    prices: []
  }),
  mutations: {
    SET_PRICES(state, data) {
      state.prices = data;
    }
  },
  actions: {
    async fetch({ commit }) {
       const res = await axios.get('/api/product-prices', {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      commit('SET_PRICES', res.data);
    },
    async create({ dispatch }, payload) {
      await axios.post('/api/product-prices', payload, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      dispatch('fetch');
    },
    async delete({ dispatch }, id) {
      await axios.delete(`/api/product-prices/${id}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      dispatch('fetch');
    }
  }
};
