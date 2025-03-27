import axios from 'axios';

export default {
  namespaced: true,
  state: {
    boms: []
  },
  mutations: {
    SET_BOMS(state, list) {
      state.boms = list;
    }
  },
  actions: {
    async fetchBoms({ commit }, productId) {
        const res = await axios.get(`/api/products/${productId}/boms`, {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
      //const res = await axios.get(`/api/products/${productId}/boms`);
      commit('SET_BOMS', res.data);
    },
    async createBom(_, bom) {
        await axios.post('/api/boms', bom, {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
    },
    async updateBom(_, bom) {
        await axios.put(`/api/boms/${bom.id}`, bom, {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
    },
    async deleteBomById(_, id) {
        await axios.delete(`/api/boms/${id}`, {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
    }
  }
};
