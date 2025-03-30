import axios from 'axios';

export default {
  namespaced: true,
  state: {
    units: []
  },
  mutations: {
    SET_UNITS(state, units) {
      state.units = units;
    }
  },
  actions: {
    async fetchUnits({ commit }) {
      const res = await axios.get('/api/units', {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      commit('SET_UNITS', res.data);
    }
  }
};
