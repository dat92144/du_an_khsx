<template>
    <div class="h-screen grid grid-cols-[auto_1fr] grid-rows-[auto_1fr] bg-gray-100">
        <!-- Sidebar -->
        <Sidebar class="row-span-2 border-r border-gray-300" />

        <!-- Header -->
        <Header class="col-span-1 border-b border-gray-300" />

        <!-- Nội dung chính -->
        <main class="p-6 overflow-auto bg-white">
            <router-view></router-view>
        </main>
    </div>
</template>


<script>
import Sidebar from '@/components/Sidebar.vue';
import Header from '@/components/Header.vue';

export default {
    components: { Sidebar, Header }
};
</script>
