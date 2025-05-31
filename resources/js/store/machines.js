// store/modules/machines.js
import axios from 'axios';

export default {
  namespaced: true,
  state: {
    machines: [],
    schedules: []
  },
  mutations: {
    SET_MACHINES(state, machines) {
      state.machines = machines;
    },
    SET_SCHEDULES(state, schedules) {
      state.schedules = schedules;
    }
  },
  actions: {
    async fetchMachines({ commit }) {
        const res = await axios.get('/api/machines', {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
      //const res = await axios.get('/api/machines');
        commit('SET_MACHINES', res.data);
    },
    async createMachine(_, data) {
        await axios.post('/api/machines', data , {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
    },
    async updateMachine(_, data) {
        await axios.put(`/api/machines/${data.id}`, data , {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
    },
    async deleteMachine(_, id) {
        await axios.delete(`/api/machines/${id}` , {
            headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
    },
    async fetchSchedules({ commit }) {
      const res = await axios.get('/api/machine-schedules', {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      commit('SET_SCHEDULES', res.data);
    }
  },
  getters: {
    machines: state => state.machines,
    schedules: state => state.schedules
  }
};
