import { createStore } from 'vuex';
import auth from './auth';
import suppliers from './suppliers';
import notifications from './notifications';
import machines from './machines';
import processes from './processes';
import products from './products';
import bomItems from './bomItems';
import boms from './boms';
import specs from './specs';
import specAttributeValues from './specAttributeValues';
import specAttributes from './specAttributes';
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
        specs,
        specAttributeValues,
        specAttributes,
    }
});

export default store;
