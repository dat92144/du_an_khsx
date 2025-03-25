import axios from 'axios';
export default {
    namespaced: true,
    state: {
        token: localStorage.getItem('auth_token') || null,
        user: JSON.parse(localStorage.getItem('user')) || null,
        role: JSON.parse(localStorage.getItem('user_role')) || [] 
    },
    mutations: {
        SET_TOKEN(state, token) {
            console.log("🔐 Lưu token vào store:", token);
            state.token = token;
            localStorage.setItem('auth_token', token);
        },
        SET_USER(state, user) {
            console.log("👤 Lưu thông tin user vào store:", user);
            state.user = user;
            localStorage.setItem('user', JSON.stringify(user));
        },
        SET_ROLE(state, role) {
            console.log("🎭 Lưu quyền user vào store:", role);
            state.role = role;
            localStorage.setItem('user_role', JSON.stringify(role));
        },
        LOGOUT(state) {
            console.log("🚪 Đăng xuất, xóa dữ liệu người dùng");
            state.token = null;
            state.user = null;
            state.role = [];
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');
            localStorage.removeItem('user_role');
        }
    },
    actions: {
        async login({ commit }, credentials) {
            try {
                console.log("📤 Gửi request đăng nhập:", credentials);
                const response = await axios.post('/api/login', credentials);

                console.log("✅ API trả về:", response.data);

                if (!response.data || !response.data.user || !response.data.token) {
                    console.error("❌ API không trả về dữ liệu hợp lệ:", response.data);
                    throw new Error("API không trả về dữ liệu hợp lệ!");
                }

                commit('SET_TOKEN', response.data.token);
                commit('SET_USER', response.data.user);
                commit('SET_ROLE', response.data.role);

                return response.data; // Quan trọng: trả về toàn bộ dữ liệu API
            } catch (error) {
                console.error("❌ Lỗi đăng nhập API:", error.response?.data || error);
                throw error;
            }
        },
        async logout({ commit }) {
            try {
                console.log("📤 Gửi request đăng xuất...");
                await axios.post('/api/logout', {}, {
                    headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
                });
                commit('LOGOUT');
            } catch (error) {
                console.error("❌ Lỗi khi đăng xuất:", error.response?.data || error);
            }
        }
    },
    getters: {
        isAuthenticated: state => !!state.token,
        userRole: state => state.role.length ? state.role[0] : null, 
        getUser: state => state.user
    }
};
