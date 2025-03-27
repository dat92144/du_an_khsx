import axios from 'axios';

export default {
  namespaced: true,
  state: {
    processes: []
  },
  mutations: {
    SET_PROCESSES(state, list) {
      state.processes = list;
    }
  },
  actions: {
    async fetchProcesses({ commit }) {
      const res = await axios.get('/api/processes', {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      //const res = await axios.get('/api/processes');
      commit('SET_PROCESSES', res.data);
    },
    async createProcess(_, data) {
      await axios.post('/api/processes', data, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      
    },
    async updateProcess(_, data) {
      await axios.put(`/api/processes/${data.id}`, data, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
    },
    async deleteProcess(_, id) {
      await axios.delete(`/api/processes/${id}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
    }
  },
  getters: {
    processes: state => state.processes
  }
};
