import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/admin.css', 'resources/js/admin.js'],
            refresh: true,
            // Self-hosted at build time: no third-party font requests from visitors.
            fonts: [
                bunny('Inter', { alias: 'inter', weights: [400, 500, 600, 700], display: 'swap' }),
                bunny('Playfair Display', { alias: 'playfair', weights: [500, 600, 700], display: 'swap', preload: false }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
