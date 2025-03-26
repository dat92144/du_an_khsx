<template>
    <aside class="bg-white shadow-md p-4 flex flex-col h-full border-r border-gray-300">
        <h2 class="text-xl font-semibold flex items-center gap-2">
            📌 Quản lý SX
        </h2>
        <nav class="mt-4 flex-1">
            <ul class="space-y-2">
                <li>
                    <router-link to="/dashboard" class="block p-3 rounded-lg bg-blue-500 text-white hover:bg-blue-600 flex items-center">
                        📊 Dashboard
                    </router-link>
                </li>
                <li>
                    <router-link to="/suppliers" class="block p-3 rounded-lg bg-gray-200 hover:bg-gray-300 flex items-center">
                        🏭 Nhà Cung Cấp
                    </router-link>
                </li>
                <li>
                    <router-link to="/materials" class="block p-3 rounded-lg bg-gray-200 hover:bg-gray-300 flex items-center">
                        📦 Định mức NVL
                    </router-link>
                </li>
                <li>
                    <router-link to="/orders" class="block p-3 rounded-lg bg-gray-200 hover:bg-gray-300 flex items-center">
                        📝 Đơn hàng SX
                    </router-link>
                </li>
                <li>
                    <router-link to="/orders" class="block p-3 rounded-lg bg-gray-200 hover:bg-gray-300 flex items-center">
                        📝 Máy móc
                    </router-link>
                </li>
            </ul>
        </nav>
    </aside>
</template>
