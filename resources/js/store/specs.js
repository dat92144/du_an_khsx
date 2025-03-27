import axios from 'axios';

export default {
  namespaced: true,
  state: {
    specs: []
  },
  mutations: {
    setSpecs(state, specs) {
      state.specs = specs;
    }
  },
  actions: {
    async fetchSpecs({ commit }) {
        const res = await axios.get('/api/specs', {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
      //const res = await axios.get('/api/specs');
      commit('setSpecs', res.data);
    },
    async createSpec({ dispatch }, data) {
        await axios.post('/api/specs', data, {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
      dispatch('fetchSpecs');
    },
    async updateSpec({ dispatch }, data) {
        await axios.put(`/api/specs/${data.id}`, data, {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
      dispatch('fetchSpecs');
    },
    async deleteSpec({ dispatch }, id) {
        await axios.delete(`/api/specs/${id}`, {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
      dispatch('fetchSpecs');
    }
  }
};
