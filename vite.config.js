import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';


export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
                ziggy: path.resolve("vendor/tightenco/ziggy/dist/index.esm.js"),
        },
    },
    /* soluciona problema de CORS por el host 0.0.0.0 en los navegadores no basados en Chromium */
    server: {
        origin: 'http://localhost:5173',
        cors: true,
        hmr: {
            host: 'localhost',
        },
    },
});
