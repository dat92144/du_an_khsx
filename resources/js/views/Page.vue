<script setup>
const logo = `${import.meta.env.BASE_URL}logo.png`;
const banner = `${import.meta.env.BASE_URL}banner.jpg`;
</script>

<template>
  <div class="home-page">
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
        <button @click="logout" class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 ">
          <LogOut class="w-4 h-4" /> Đăng xuất
        </button>
      </nav>
    </header>

    <section
      class="hero d-flex flex-column justify-content-center align-items-center text-center text-white"
      :style="{
        background: `url(${banner}) no-repeat center center / cover`,
        height: '100vh',
        paddingTop: '100px'
      }"
    >
      <div class="mb-2" style="height: 3px; width: 80px; background-color: red;"></div>

      <h6 class="text-uppercase text-light fw-light" style="letter-spacing: 1px;">
        Chào mừng đến với Xi măng Thăng Long
      </h6>

      <h1 class="fw-bold display-4 text-shadow text-dark" style="text-shadow: 2px 2px 4px #fff;">
        CÔNG TY CỔ PHẦN XI MĂNG THĂNG LONG
      </h1>

      <p class="mt-3 w-75 text-dark fs-5" style="max-width: 900px;">
        Tham gia với chúng tôi trên con đường trở thành chất xúc tác cho sự thay đổi bằng cách thúc đẩy sự đổi mới và tạo ra các giải pháp cho một tương lai tốt hơn và bền vững hơn.
      </p>
    </section>
  </div>
</template>
<script>
    export default{
        methods:{
            logout() {
                localStorage.removeItem("auth_token");
                this.$router.push("/");
            }
        }
    };
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
