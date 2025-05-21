<template>
  <div>
    <HeaderPublic />
    <main style="margin-top: 80px">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import HeaderPublic from '@/components/public/HeaderPublic.vue';
</script>
