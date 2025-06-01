import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path'; // <--- 1. เพิ่มการ import 'path'

export default defineConfig({
    plugins: [
        laravel({
             input: 'resources/js/app.js', // ถ้ามีหลาย entry points ให้ใช้ array
            //input: ['resources/css/app.css', 'resources/js/app.js'], // ตัวอย่างถ้ามี CSS ด้วย
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    // --- 2. เพิ่มส่วน resolve.alias ตรงนี้ ---
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            // คุณอาจจะมี alias อื่นๆ อีกในอนาคต เช่น 'ziggy-js'
            // 'ziggy': path.resolve(__dirname, 'vendor/tightenco/ziggy/dist/vue.es.js'), // ตัวอย่างถ้าใช้ Ziggy
        },
    },
    // --- สิ้นสุดส่วน resolve.alias ---
});
