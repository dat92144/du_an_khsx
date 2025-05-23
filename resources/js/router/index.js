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
import Forbidden from '../views/Forbidden.vue';
import Register from '../views/Register.vue';
import MachineMonitor from '../views/MachineMonitor.vue';
import ProductCostHistory from '../views/ProductCostHistory.vue';
import ProductCostList from '../views/ProductCostList.vue';
import ProductPriceList from '../views/ProductPriceList.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import Home from '@/views/public/Home.vue';
const routes = [
    { path: '/login', component: Login },
    { path: '/forbidden', component: Forbidden },
    { path: '/register', component: Register },
    {
        path: '/internal',
        component: main,
        meta: { requiresAuth: true, role: 'Admin' },
        beforeEnter: (to, from, next) => {
            const isAuth = store.getters['auth/isAuthenticated'];
            const userRole = store.getters['auth/userRole'];
            if (!isAuth) return next('/login');
            const matchedRoute = to.matched.find(r => r.meta?.role);
            const requiredRole = matchedRoute?.meta.role;
            if (requiredRole && !userRole) {
                console.warn('🚫 Người dùng không có quyền nào nhưng cố truy cập');
                return next('/forbidden');
            }
            if (requiredRole && userRole !== requiredRole) {
                console.warn(`🚫 Cấm truy cập: cần quyền ${requiredRole}, bạn là ${userRole}`);
                return next('/forbidden');
            }
            next();
        },
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
    },
    {
        path: '/',
        component: PublicLayout,
        children: [
            { path: '', name: 'public.home', component: Home},
            //{ path: 'about', name: 'public.about', component: About },
            //{ path: 'contact', name: 'public.contact', component: Contact },
            //{ path: 'products/:id', name: 'public.product.detail', component: ProductDetail },
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
