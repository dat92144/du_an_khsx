<template>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-sm" style="width: 400px;">
            <h3 class="text-center mb-3">🔐 Đăng nhập</h3>
            <div v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>

            <form @submit.prevent="handleLogin">
                <div class="mb-3">
                    <label>Email:</label>
                    <input v-model="email" type="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Mật khẩu:</label>
                    <input v-model="password" type="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100" :disabled="isLoading">
                    <span v-if="isLoading">⏳ Đang đăng nhập...</span>
                    <span v-else>Đăng nhập</span>
                </button>
            </form>
        </div>
    </div>
</template>

<script>
import { useStore } from 'vuex';
import { ref } from 'vue';
import { useRouter } from 'vue-router';

export default {
    setup() {
        const store = useStore(); // Lấy Vuex Store
        const router = useRouter(); // Vue Router để điều hướng

        const email = ref('');
        const password = ref('');
        const errorMessage = ref('');
        const isLoading = ref(false);
        const handleLogin = async () => {
            if (isLoading.value) return;
            isLoading.value = true;
            errorMessage.value = '';

            try {
                console.log("📤 Gửi dữ liệu đăng nhập:", { email: email.value, password: password.value });

                const response = await store.dispatch('auth/login', {
                    email: email.value,
                    password: password.value
                });

                console.log("✅ API trả về dữ liệu:", response);

                if (!response || !response.user || !response.token) {
                    console.error("❌ API không trả về user/token hợp lệ:", response);
                    errorMessage.value = "Lỗi hệ thống, vui lòng thử lại!";
                    return;
                }

                console.log("✅ User:", response.user);
                console.log("✅ Role:", response.role);
                console.log("✅ Token:", response.token);

                if (response.role.includes("Admin")) {
                    router.push('/dashboard');
                } else {
                    router.push('/');
                }
            } catch (error) {
                console.error("❌ Lỗi khi đăng nhập:", error.response?.data || error);
                errorMessage.value = "Sai tài khoản hoặc mật khẩu!";
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
