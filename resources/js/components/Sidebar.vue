<template>
    <aside class="bg-white shadow-md p-4 flex flex-col h-full border-r border-gray-300">
      <h2 class="text-xl font-semibold flex items-center gap-2">
        <Pin class="w-5 h-5" /> Quản lý SX
      </h2>
      <nav class="mt-4 flex-1">
        <ul class="space-y-2">
          <li>
            <router-link
              to="/dashboard"
              class="block p-3 rounded-lg bg-blue-500 text-white hover:bg-blue-600 flex items-center gap-2"
            >
              <BarChart2 class="w-5 h-5" /> Dashboard
            </router-link>
          </li>
          <li>
            <router-link
              to="/suppliers"
              class="block p-3 rounded-lg bg-gray-200 hover:bg-gray-300 flex items-center gap-2"
            >
              <Factory class="w-5 h-5" /> Nhà Cung Cấp
            </router-link>
          </li>
          <li>
            <router-link
              to="/orders"
              class="block p-3 rounded-lg bg-gray-200 hover:bg-gray-300 flex items-center gap-2"
            >
              <ClipboardList class="w-5 h-5" /> Đơn hàng SX
            </router-link>
          </li>
          <li>
            <router-link
              to="/machines"
              class="block p-3 rounded-lg bg-gray-200 hover:bg-gray-300 flex items-center gap-2"
            >
              <Settings class="w-5 h-5" /> Máy Móc
            </router-link>
          </li>
          <li>
            <router-link
              to="/processes"
              class="block p-3 rounded-lg bg-gray-200 hover:bg-gray-300 flex items-center gap-2"
            >
              <Repeat class="w-5 h-5" /> Công Đoạn
            </router-link>
          </li>
          <li>
            <router-link
              to="/raw-materials"
              class="block p-3 rounded-lg bg-gray-200 hover:bg-gray-300 flex items-center gap-2"
            >
              <Boxes class="w-5 h-5" /> Nguyên Vật Liệu
            </router-link>
          </li>
          <li>
            <router-link
              to="/products"
              class="block p-3 rounded-lg bg-gray-200 hover:bg-gray-300 flex items-center gap-2"
            >
              <Package class="w-5 h-5" /> Sản phẩm
            </router-link>
          </li>
          <li>
            <router-link
              to="/production-orders"
              class="block p-3 rounded-lg bg-gray-200 hover:bg-gray-300 flex items-center gap-2"
            >
              <ClipboardCheck class="w-5 h-5" /> Kế hoạch SX
            </router-link>
          </li>
          <li>
          <router-link
            to="/machine-monitor"
            class="block p-3 rounded-lg bg-gray-200 hover:bg-gray-300 flex items-center gap-2"
          >
            <Activity class="w-5 h-5" /> Giám sát máy
          </router-link>
          </li>
        </ul>
      </nav>
    </aside>
  </template>

  <script>
  import {
    BarChart2,
    Package,
    Factory,
    ClipboardList,
    ClipboardCheck,
    Boxes,
    Pin,
    Repeat,
    Settings,
    Activity
  } from 'lucide-vue-next';

  export default {
    components: {
      BarChart2,
      Package,
      Factory,
      ClipboardList,
      ClipboardCheck,
      Boxes,
      Pin,
      Repeat,
      Settings,
      Activity
    }
  };
  </script>
