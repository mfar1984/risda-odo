import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/css/mobile.css', 
                'resources/js/app.js',
                'resources/js/tuntutan-actions.js',
                'resources/js/program-actions.js',
                'resources/js/delete-actions.js',
                'resources/js/support-actions.js',
                'resources/js/audit-trail.js'
            ],
            refresh: true,
        }),
    ],
    build: {
        // Optimize chunk splitting (increased limit for vendor bundles)
        chunkSizeWarningLimit: 1500,
        rollupOptions: {
            output: {
                manualChunks: (id) => {
                    // Vendor chunks - separate large libraries
                    if (id.includes('node_modules')) {
                        // Alpine.js
                        if (id.includes('alpinejs')) {
                            return 'vendor-alpine';
                        }
                        // Axios
                        if (id.includes('axios')) {
                            return 'vendor-axios';
                        }
                        // jQuery
                        if (id.includes('jquery')) {
                            return 'vendor-jquery';
                        }
                        // Leaflet (map library)
                        if (id.includes('leaflet')) {
                            return 'vendor-leaflet';
                        }
                        // MapTiler SDK
                        if (id.includes('@maptiler')) {
                            return 'vendor-maptiler';
                        }
                        // Tagify (tags input)
                        if (id.includes('tagify')) {
                            return 'vendor-tagify';
                        }
                        // Chart.js
                        if (id.includes('chart.js')) {
                            return 'vendor-chart';
                        }
                        // Malaysia Postcodes
                        if (id.includes('malaysia-postcodes')) {
                            return 'vendor-postcodes';
                        }
                        // Tailwind & Forms
                        if (id.includes('tailwindcss') || id.includes('@tailwindcss')) {
                            return 'vendor-tailwind';
                        }
                        // Other vendors
                        return 'vendor-common';
                    }
                },
                // Optimize chunk names
                chunkFileNames: 'assets/[name]-[hash].js',
                entryFileNames: 'assets/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash].[ext]',
            },
        },
        // TEMPORARY: Disable minification to fix Alpine.js errors
        // TODO: Re-enable with proper config later
        minify: false,
        // Use modern target so Alpine's dynamic AsyncFunction yields real Promises
        target: 'es2017',
        // CSS optimization
        cssMinify: true,
        cssCodeSplit: true,
    },
    css: {
        devSourcemap: false,
    },
});
