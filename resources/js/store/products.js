import axios from 'axios';

export default {
  namespaced: true,
  state: {
    products: [],
    semiProducts: []
  },
  mutations: {
    SET_PRODUCTS(state, list) {
      state.products = list;
    },
    SET_SEMI_PRODUCTS(state, list) {
      state.semiProducts = list;
    }
  },
  actions: {
    async fetchProducts({ commit }) {
      const res = await axios.get('/api/products', {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      commit('SET_PRODUCTS', res.data);
    },

    async fetchSemiProducts({ commit }) {
      const res = await axios.get('/api/semi-finished-products', {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      commit('SET_SEMI_PRODUCTS', res.data);
    },

    async createProduct(_, data) {
      await axios.post('/api/products', data, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
    },

    async updateProduct(_, data) {
      await axios.put(`/api/products/${data.id}`, data, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
    },

    async deleteProductById(_, id) {
      await axios.delete(`/api/products/${id}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
    }
  }
};
