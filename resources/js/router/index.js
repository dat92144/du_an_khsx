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

const routes = [
    { path: '/', redirect: '/login' },
    { path: '/login', component: Login },
    {
        path: '/',
        component: main,
        children: [
            { path: '/dashboard', component: Dashboard, meta: { requiresAuth: true, role: 'Admin'} },
            { path: '/suppliers', component: Suppliers, meta: { requiresAuth: true, role: 'admin' } },
            { path: '/materials', component: Materials, meta: { requiresAuth: true } },
            { path: '/orders', component: OrderList, meta: { requiresAuth: true } },
            { path: '/machines', component: Machines, meta: { requiresAuth: true } },
            { path: '/processes', component: Processes, meta: { requiresAuth: true } },
            { path: '/raw-materials', component: RawMaterials, meta: { requiresAuth: true } },
            { path: '/products', component: ProductList, meta: { requiresAuth: true } },
            { path: '/production-orders', component: ProductionOrders, meta: { requiresAuth: true } },

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

    // 1. Nếu route yêu cầu đăng nhập
    if (to.meta.requiresAuth) {
        if (!isAuth) {
            // Nếu chưa đăng nhập → chuyển về login
            return next('/login');
        }

        // 2. Nếu route yêu cầu role cụ thể
        if (to.meta.role && to.meta.role !== userRole) {
            console.warn(`❌ Truy cập bị chặn: cần quyền "${to.meta.role}", bạn là "${userRole}"`);
            return next('/login'); // hoặc redirect đến /forbidden nếu bạn có trang đó
        }
    }

    // 3. Cho phép đi tiếp nếu không bị chặn
    next();
});
export default router;
