import axios from 'axios';

export default {
  namespaced: true,
  state: {
    specAttributeValues: []
  },
  mutations: {
    setSpecAttributeValues(state, data) {
      state.specAttributeValues = data;
    }
  },
  actions: {
    async fetchSpecAttributeValues({ commit }) {
      const res = await axios.get('/api/spec-attribute-values', {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      //const res = await axios.get('/api/spec-attribute-values');
      commit('setSpecAttributeValues', res.data);
    },
    async createSpecAttributeValue({ dispatch }, data) {
      await axios.post('/api/spec-attribute-values', data, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      dispatch('fetchSpecAttributeValues');
    },
    async updateSpecAttributeValue({ dispatch }, data) {
      await axios.put(`/api/spec-attribute-values/${data.id}`, data, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      dispatch('fetchSpecAttributeValues');
    },
    async deleteSpecAttributeValue({ dispatch }, id) {
      await axios.delete(`/api/spec-attribute-values/${id}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      dispatch('fetchSpecAttributeValues');
    }
  }
};
