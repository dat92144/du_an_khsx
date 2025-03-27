import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../views/Dashboard.vue';
import Suppliers from '../views/Suppliers.vue';
import Materials from '../views/Materials.vue';
import Orders from '../views/Orders.vue';
import Login from '../views/Login.vue';
import main from '@/layouts/main.vue';
import Machines from '../views/Machines.vue';
import Processes from '../views/Processes.vue';
import RawMaterials from '../views/RawMaterials.vue';
import ProductList from '../views/ProductList.vue';
const routes = [
    { path: '/', redirect: '/login' },
    { path: '/login', component: Login },
    {
        path: '/',
        component: main,
        children: [
            { path: '/dashboard', component: Dashboard, meta: { requiresAuth: true } },
            { path: '/suppliers', component: Suppliers, meta: { requiresAuth: true } },
            { path: '/materials', component: Materials, meta: { requiresAuth: true } },
            { path: '/orders', component: Orders, meta: { requiresAuth: true } },
            { path: '/machines', component: Machines, meta: { requiresAuth: true } },
            { path: '/processes', component: Processes, meta: { requiresAuth: true } },
            { path: '/raw-materials', component: RawMaterials, meta: { requiresAuth: true } },
            { path: '/products', component: ProductList, meta: { requiresAuth: true } },
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
