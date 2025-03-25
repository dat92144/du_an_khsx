import { createApp } from 'vue';
import App from './App.vue';
import store from './store'; // Import Vuex store
import router from './router'; // Import Vue Router
import '../css/app.css';

const app = createApp(App);

app.use(store); // Đăng ký Vuex store vào ứng dụng
app.use(router); // Đăng ký Vue Router

app.mount('#app');
