<template>
    <div class="container d-flex justify-content-center align-items-center vh-100">
      <div class="card p-4 shadow-sm" style="width: 400px;">
        <h3 class="text-center mb-3 flex items-center justify-center gap-2">
          <Lock class="w-5 h-5" /> Đăng nhập
        </h3>
        <div v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>

        <form @submit.prevent="handleLogin">
          <div class="mb-3">
            <label>Email:</label>
            <input v-model="email" type="email" class="form-control" required />
          </div>
          <div class="mb-3">
            <label>Mật khẩu:</label>
            <input v-model="password" type="password" class="form-control" required />
          </div>
          <button type="submit" class="btn btn-primary w-100 d-flex justify-content-center align-items-center gap-2" :disabled="isLoading">
            <Loader v-if="isLoading" class="animate-spin w-4 h-4" />
            <span>{{ isLoading ? 'Đang đăng nhập...' : 'Đăng nhập' }}</span>
          </button>
        </form>
      </div>
    </div>
  </template>

  <script>
  import { ref } from 'vue';
  import { useStore } from 'vuex';
  import { useRouter } from 'vue-router';
  import { Lock, Loader } from 'lucide-vue-next';

  export default {
    components: {
      Lock,
      Loader
    },
    setup() {
      const store = useStore();
      const router = useRouter();

      const email = ref('');
      const password = ref('');
      const errorMessage = ref('');
      const isLoading = ref(false);

      const handleLogin = async () => {
        if (isLoading.value) return;
        isLoading.value = true;
        errorMessage.value = '';

        try {
          const response = await store.dispatch('auth/login', {
            email: email.value,
            password: password.value
          });

          if (!response || !response.user || !response.token) {
            errorMessage.value = 'Lỗi hệ thống, vui lòng thử lại!';
            return;
          }

          if (response.role && response.role.includes('Admin')) {
            router.push('/internal');
          } else if (!response.role || !response.role.length){
            errorMessage.value = 'Tài khoản không có quyền truy cập hệ thống';
            return;
          } else {
            router.push('/');
        }
        } catch (error) {
          errorMessage.value = 'Sai tài khoản hoặc mật khẩu!';
          console.error('❌ Lỗi khi đăng nhập:', error.response?.data || error);
        } finally {
          isLoading.value = false;
        }
      };

      return {
        email,
        password,
        errorMessage,
        isLoading,
        handleLogin
      };
    }
  };
  </script>
