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
                'resources/css/select2.min.css',
                'resources/js/app.js',
                'resources/js/form-select2.js',
                'resources/js/select2.full.min.js',
            ],
            refresh: true,
        }),
    ],
    optimizeDeps: {
        include: ['jquery'],
      },
});
