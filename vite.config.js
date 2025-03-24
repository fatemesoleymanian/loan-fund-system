import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { quasar } from '@quasar/vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/frontend/src/css/app.scss',
                'resources/frontend/src/main.js'
            ],
            refresh: true,
        }),
        quasar({
            sassVariables: 'resources/frontend/src/css/quasar.variables.scss'
        })
    ],
    resolve: {
        alias: {
            '@': '/resources/frontend/src',
            quasar: 'quasar/dist/quasar.esm.prod.js'
        }
    }
});
