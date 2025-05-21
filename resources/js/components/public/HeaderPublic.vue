<template>
  <header
  class="d-flex justify-content-between align-items-center px-5 py-3 bg-white shadow position-fixed top-0 w-100"
  style="z-index: 10;"
>
  <img :src="logo" alt="Logo" style="height: 50px;" />
  <nav class="d-flex gap-4 align-items-center fw-medium">
    <a href="#">Trang chủ</a>
    <a href="#">Giới thiệu</a>
    <a href="#">Sản phẩm</a>
    <a href="#">Cung ứng</a>
    <a href="#">Tuyển dụng</a>
    <a href="#">Liên hệ</a>

    <template v-if="isLoggedIn">
      <router-link to="/account" class="btn btn-outline-primary">Tài khoản</router-link>
      <button @click="logout" class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 d-flex align-items-center gap-1">
        <LogOut class="w-4 h-4" /> Đăng xuất
      </button>
    </template>
    <template v-else>
      <router-link to="/login" class="btn btn-outline-primary">Đăng nhập</router-link>
      <router-link to="/register" class="btn btn-primary">Đăng ký</router-link>
    </template>
  </nav>
</header>

</template>

<script setup>
const logo = `${import.meta.env.BASE_URL}logo.png`;
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { LogOut } from 'lucide-vue-next';

const router = useRouter();
const token = ref(localStorage.getItem('auth_token'));

const isLoggedIn = computed(() => !!token.value);

function logout() {
  localStorage.removeItem('auth_token');
  token.value = null;
  router.push('/');
}
</script>


<style scoped>
a {
  text-decoration: none;
  color: #333;
}
a:hover {
  color: #007bff;
}
</style>
