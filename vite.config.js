import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = fileURLToPath(new URL('.', import.meta.url));

export default defineConfig({
 
    plugins: [
        laravel({
            input: [
                "resources/scss/custom.scss",
                "resources/js/custom.js",
                /* Doctor Panel — isolated CSS bundles */
                "resources/css/doctor/doctorsidebar.css",
                "resources/css/doctor/doctordashboard.css",
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            '@modules': path.resolve(__dirname, 'resources/js/modules'),
            '@utils': path.resolve(__dirname, 'resources/js/utils'),
        },
    },
    build: {
        chunkSizeWarningLimit: 100000000,
    },
});