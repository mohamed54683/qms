import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
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
    build: {
        // Performance optimizations
        cssCodeSplit: true,
        rollupOptions: {
            output: {
                manualChunks: {
                    // Separate vendor chunks for better caching
                    vendor: ['vue', '@inertiajs/vue3'],
                }
            }
        }
    },
    server: {
        // Performance optimizations for development
        hmr: {
            overlay: false
        },
        // Enable compression
        compress: true
    }
});
