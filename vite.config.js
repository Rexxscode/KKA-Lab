import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/ai/object-detector.js', 'resources/js/ai/trainer.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
