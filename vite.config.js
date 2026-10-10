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
    build: {
        // Inti TensorFlow (~MB) dipakai object-detector + trainer:
        // satukan dalam 1 chunk agar tidak diduplikat di 2 bundle.
        rollupOptions: {
            output: {
                manualChunks: {
                    tf: ['@tensorflow/tfjs'],
                },
            },
        },
    },
});
