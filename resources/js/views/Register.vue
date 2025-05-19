<template>
  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow-sm" style="width: 400px;">
      <h3 class="text-center mb-3">Đăng ký tài khoản</h3>

      <div v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>
      <div v-if="successMessage" class="alert alert-success">{{ successMessage }}</div>

      <form @submit.prevent="handleRegister">
        <div class="mb-2">
          <label>Họ tên:</label>
          <input v-model="username" type="text" class="form-control" />
        </div>

        <div class="mb-2">
          <label>Email:</label>
          <input v-model="email" type="email" class="form-control" />
        </div>

        <div class="mb-2 d-flex gap-2 align-items-center">
          <input v-model="verifyCode" placeholder="Mã xác nhận" class="form-control" />
          <button class="btn btn-sm btn-outline-primary" type="button" @click="sendCode" :disabled="sending">
            {{ sending ? 'Đang gửi...' : 'Gửi mã' }}
          </button>
        </div>

        <div class="mb-2">
          <label>Mật khẩu:</label>
          <input v-model="password" type="password" class="form-control" />
        </div>

        <div class="mb-2">
          <label>Xác nhận mật khẩu:</label>
          <input v-model="confirmPassword" type="password" class="form-control" />
        </div>

        <button type="submit" class="btn btn-primary w-100" :disabled="isLoading">
          {{ isLoading ? 'Đang đăng ký...' : 'Đăng ký' }}
        </button>
      </form>

      <div class="text-center mt-3">
        <router-link to="/login">Đã có tài khoản? Đăng nhập</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';

const store = useStore();
const router = useRouter();

const username = ref('');
const email = ref('');
const password = ref('');
const confirmPassword = ref('');
const verifyCode = ref('');

const errorMessage = ref('');
const successMessage = ref('');
const isLoading = ref(false);
const sending = ref(false);

const sendCode = async () => {
  if (!email.value) {
    errorMessage.value = 'Vui lòng nhập email trước khi gửi mã xác nhận';
    return;
  }

  sending.value = true;
  errorMessage.value = '';
  successMessage.value = '';

  try {
    await store.dispatch('auth/sendVerificationCode', email.value);
    successMessage.value = '✅ Mã xác nhận đã được gửi tới email!';
  } catch (err) {
    errorMessage.value = err.message || 'Gửi mã thất bại';
  } finally {
    sending.value = false;
  }
};

const handleRegister = async () => {
  errorMessage.value = '';
  successMessage.value = '';
  isLoading.value = true;

  if (!verifyCode.value) {
    errorMessage.value = 'Vui lòng nhập mã xác nhận';
    isLoading.value = false;
    return;
  }

  if (password.value !== confirmPassword.value) {
    errorMessage.value = 'Mật khẩu xác nhận không khớp';
    isLoading.value = false;
    return;
  }

  try {
    await store.dispatch('auth/register', {
      username: username.value,
      email: email.value,
      password: password.value,
      verify_code: verifyCode.value
    });

    successMessage.value = 'Đăng ký thành công! Đang chuyển hướng...';
    setTimeout(() => router.push('/login'), 2000);
  } catch (err) {
    errorMessage.value =
      err.response?.data?.message || 'Đăng ký không thành công. Vui lòng thử lại!';
  } finally {
    isLoading.value = false;
  }
};
</script>
