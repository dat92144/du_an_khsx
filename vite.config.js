import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/assets/tailwind.css'],
            refresh: true,
        }),
        vue(), // Đảm bảo plugin Vue được gọi đúng
    ],
});
