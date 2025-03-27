import { createStore } from 'vuex';
import auth from './auth';
import suppliers from './suppliers';
import notifications from './notifications';
import machines from './machines';
import processes from './processes';
import products from './products';
import bomItems from './bomItems';
import boms from './boms';
const store = createStore({
    modules: {
        auth,
        suppliers,
        notifications,
        machines,
        processes,
        products,
        bomItems,
        boms,
    }
});

export default store;
