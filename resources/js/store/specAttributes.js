import axios from 'axios';

export default {
  namespaced: true,
  state: {
    specAttributes: []
  },
  mutations: {
    setSpecAttributes(state, data) {
      state.specAttributes = data;
    }
  },
  actions: {
    async fetchSpecAttributes({ commit }) {
      console.log('🚀 Gọi fetchSpecAttributes...');
        const res = await axios.get('/api/spec-attributes', {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
      //const res = await axios.get('/api/spec-attributes');
      console.log('📦 Kết quả API:', res.data);
      commit('setSpecAttributes', res.data);
    },
    async createSpecAttribute({ dispatch }, data) {
        await axios.post('/api/spec-attributes', data, {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
      dispatch('fetchSpecAttributes');
    },
    async updateSpecAttribute({ dispatch }, data) {
        await axios.put(`/api/spec-attributes/${data.id}`, data, {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
      dispatch('fetchSpecAttributes');
    },
    async deleteSpecAttribute({ dispatch }, id) {
        await axios.delete(`/api/spec-attributes/${id}`, {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
      dispatch('fetchSpecAttributes');
    }
  }
};
