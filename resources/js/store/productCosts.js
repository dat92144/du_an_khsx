import axios from 'axios';

export default {
  namespaced: true,
  state: () => ({
    costs: []
  }),
  mutations: {
    SET_COSTS(state, data) {
      state.costs = data;
    }
  },
  actions: {
    async fetch({ commit }) {
      const res = await axios.get('/api/product-costs', {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      commit('SET_COSTS', res.data);
    },
    async create({ dispatch }, payload) {
      await axios.post('/api/product-costs', payload, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      dispatch('fetch');
    },
    async update({ dispatch }, payload) {
      await axios.put(`/api/product-costs/${payload.id}`, payload, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      dispatch('fetch');
    },
    async delete({ dispatch }, id) {
      await axios.delete(`/api/product-costs/${id}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      dispatch('fetch');
    }
  }
};
