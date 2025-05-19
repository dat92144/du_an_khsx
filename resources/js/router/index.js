import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../views/Dashboard.vue';
import Suppliers from '../views/Suppliers.vue';
import Materials from '../views/Materials.vue';
import OrderList from '../views/OrderList.vue';
import Login from '../views/Login.vue';
import main from '@/layouts/main.vue';
import Machines from '../views/Machines.vue';
import Processes from '../views/Processes.vue';
import RawMaterials from '../views/RawMaterials.vue';
import ProductList from '../views/ProductList.vue';
import ProductionOrders from '../views/ProductionOrders.vue';
import store from '../store';
import Home from '../views/Home.vue';
import Forbidden from '../views/Forbidden.vue';
import Register from '../views/Register.vue';
import Page from '../views/Page.vue';
import MachineMonitor from '../views/MachineMonitor.vue';
import ProductCostHistory from '../views/ProductCostHistory.vue';
import ProductCostList from '../views/ProductCostList.vue';
import ProductPriceList from '../views/ProductPriceList.vue';

const routes = [
    // { path: '/', redirect: '/login' },
    // { path: '/login', component: Login },

    { path: '/', component: Home },
    { path: '/login', component: Login },
    { path: '/forbidden', component: Forbidden },
    { path: '/register', component: Register },
    { path: '/page', component: Page, meta: { requiresAuth: true,} },
    {
        path: '/',
        component: main,
        children: [
            { path: '/dashboard', component: Dashboard, meta: { requiresAuth: true, role: 'Admin'} },
            { path: '/suppliers', component: Suppliers, meta: { requiresAuth: true, role: 'Admin' } },
            { path: '/materials', component: Materials, meta: { requiresAuth: true } },
            { path: '/orders', component: OrderList, meta: { requiresAuth: true, role: 'Admin' } },
            { path: '/machines', component: Machines, meta: { requiresAuth: true, role: 'Admin' } },
            { path: '/processes', component: Processes, meta: { requiresAuth: true, role: 'Admin' } },
            { path: '/raw-materials', component: RawMaterials, meta: { requiresAuth: true, role: 'Admin' } },
            { path: '/products', component: ProductList, meta: { requiresAuth: true, role: 'Admin' } },
            { path: '/production-orders', component: ProductionOrders, meta: { requiresAuth: true, role: 'Admin' } },
            { path: '/machine-monitor', component: MachineMonitor, meta: { requiresAuth: true, role: 'Admin' } },
            { path: '/product-costs', component: ProductCostList, meta: { requiresAuth: true, role: 'Admin' } },
            { path: '/product-cost-histories', component: ProductCostHistory, meta: { requiresAuth: true, role: 'Admin' } },
            { path: '/product-prices', component: ProductPriceList, meta: { requiresAuth: true, role: 'Admin' } },

        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    const isAuth = store.getters['auth/isAuthenticated'];
    const userRole = store.getters['auth/userRole'];
    if (to.meta.requiresAuth) {
        if (!isAuth) {
            return next('/login');
        }

        if (to.meta.role && to.meta.role !== userRole) {
            console.warn(`❌ Truy cập bị chặn: cần quyền "${to.meta.role}", bạn là "${userRole}"`);
            return next('/forbidden'); 
        }
    }
    next();
});
export default router;
