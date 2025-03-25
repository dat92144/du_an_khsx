import { createStore } from 'vuex';
import auth from './auth';
import suppliers from './suppliers';
import notifications from './notifications';

const store = createStore({
    modules: {
        auth,
        suppliers,
        notifications
    }
});

export default store;
