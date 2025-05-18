import axios from 'axios';

export default {
  namespaced: true,
  state: {
    orders: [],
  },
  mutations: {
    SET_ORDERS(state, list) {
      state.orders = list;
    },
    ADD_ORDER(state, order) {
      state.orders.push(order);
    },
    UPDATE_ORDER(state, updated) {
      const idx = state.orders.findIndex(o => o.id === updated.id);
      if (idx !== -1) state.orders[idx] = updated;
    },
    DELETE_ORDER(state, id) {
      state.orders = state.orders.filter(o => o.id !== id);
    },
  },
  actions: {
    async fetchOrders({ commit }) {
      const res = await axios.get('/api/orders', {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      commit('SET_ORDERS', res.data);
    },

    async createOrder({ dispatch }, data) {
      await axios.post('/api/orders', data, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      await dispatch('fetchOrders');
    },

    async updateOrder({ dispatch }, data) {
      await axios.put(`/api/orders/${data.id}`, data, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      await dispatch('fetchOrders');
    },

    async deleteOrder({ dispatch }, id) {
      try{
        await axios.delete(`/api/orders/${id}`, {
          headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
        await dispatch('fetchOrders');
      }catch (error) {
        console.error('❌ Lỗi khi xoá đơn hàng:', error);
      }
    },

    async produceOrder({ dispatch }, id) {
      await axios.post(`/api/orders/${id}/start-production`, {}, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      await dispatch('fetchOrders');
    },
    async updateEstimatedDelivery() {
      if (!this.form || !this.form.details || this.form.details.length === 0) return;

      try {
        const res = await axios.post('/api/orders/estimate-delivery', {
          details: this.form.details
        }, {
          headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });

        this.form = {
          ...this.form,
          delivery_date: res.data.delivery_date?.slice(0, 10)
        };
      } catch (err) {
        console.error('❌ Không thể ước lượng ngày giao hàng:', err);
      }
    }




  }
};
