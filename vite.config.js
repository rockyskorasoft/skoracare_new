import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
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
    css: {
        preprocessorOptions: {
            scss: {
                api: 'modern-compiler',
                silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'if-function'],
            },
        },
    },
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            '@modules': path.resolve(__dirname, 'resources/js/modules'),
            '@utils': path.resolve(__dirname, 'resources/js/utils'),
        },
    },
    build: {
        chunkSizeWarningLimit: 1500,
        rolldownOptions: {
            checks: {
                pluginTimings: false,
            },
        },
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        if (id.includes('ckeditor5')) {
                            return 'vendor-ckeditor';
                        }
                        if (id.includes('datatables.net') || id.includes('jquery') || id.includes('select2') || id.includes('daterangepicker')) {
                            return 'vendor-datatables';
                        }
                        if (id.includes('@fortawesome') || id.includes('bootstrap-icons')) {
                            return 'vendor-icons';
                        }
                        return 'vendor';
                    }
                }
            }
        }
    },
});