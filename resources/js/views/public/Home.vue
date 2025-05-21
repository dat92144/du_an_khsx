<script setup>
const banner = `${import.meta.env.BASE_URL}banner.jpg`;
</script>

<template>
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
</template>

<style scoped>
.text-shadow {
  text-shadow: 2px 2px 4px #fff;
}
</style>
