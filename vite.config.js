import { defineConfig } from 'vite';
import inject from "@rollup/plugin-inject";
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        inject({   // => that should be first under plugins array
            $: 'jquery',
            jQuery: 'jquery',
        }),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    optimizeDeps: {
        include: ['jquery'],
      },
});
