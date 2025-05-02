import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import store from './store';
import axios from 'axios';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import './assets/tailwind.css';

axios.defaults.baseURL = 'http://127.0.0.1:8000'; // Đảm bảo đúng địa chỉ Laravel
//axios.defaults.withCredentials = true; // Bắt buộc để gửi cookie chứa CSRF token
//axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const app = createApp(App);
app.use(router);
app.use(store);

app.mount('#app');

export default axios;
