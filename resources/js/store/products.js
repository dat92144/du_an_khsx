import axios from 'axios';

export default {
  namespaced: true,
  state: {
    products: []
  },
  mutations: {
    SET_PRODUCTS(state, list) {
      state.products = list;
    }
  },
  actions: {
    async fetchProducts({ commit }) {
        const res = await axios.get('/api/products', {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
          });
      //const res = await axios.get('/api/products');
      commit('SET_PRODUCTS', res.data);
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
