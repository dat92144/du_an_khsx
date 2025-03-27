import axios from 'axios';

export default {
  namespaced: true,
  state: {
    bomItems: []
  },
  mutations: {
    SET_BOM_ITEMS(state, items) {
      state.bomItems = items;
    }
  },
  actions: {
    async fetchBomItems({ commit }, bomId) {
        const res = await axios.get(`/api/boms/${bomId}/items`, {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
      //const res = await axios.get(`/api/boms/${bomId}/items`);
      commit('SET_BOM_ITEMS', res.data);
    },
    async createBomItem(_, data) {
        await axios.post('/api/bom-items', data, {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
    },
    async updateBomItem(_, data) {
        await axios.put(`/api/bom-items/${data.id}`, data, {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
    },
    async deleteBomItem(_, id) {
        await axios.delete(`/api/bom-items/${id}`, {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
    }
  }
};
