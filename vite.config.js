import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.tsx',
            refresh: true,
        }),
        react(),
    ],
    server: {
        cors: {
            // Replace 'your-site.test' with your actual Herd site name
            origin: /^https?:\/\/(?:(?:[^\:]+\.)?localhost|bancotiempo_voluntariados\.test|127\.0\.0\.1|\[::1\])(?::\d+)?$/,
        },
    },
});
