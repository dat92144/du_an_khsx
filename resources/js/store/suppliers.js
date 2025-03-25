import axios from 'axios';

export default {
    namespaced: true,
    state: {
        suppliers: []
    },
    mutations: {
        SET_SUPPLIERS(state, suppliers) {
            state.suppliers = suppliers;
        }
    },
    actions: {
        async fetchSuppliers({ commit }) {
            const response = await axios.get('/api/supplier', {
                headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
            });
            commit('SET_SUPPLIERS', response.data);
        },

        async fetchSupplierPrices(_, supplierId){
            const response = await axios.get(`/api/supplierprice/${supplierId}`, {
                headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
            });
            return response.data;
        }
    },
    getters: {
        getSuppliers: state => state.suppliers
    }
};
