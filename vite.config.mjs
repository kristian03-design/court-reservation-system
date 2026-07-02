import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { existsSync, readdirSync, statSync } from 'node:fs';
import { join } from 'node:path';

function collectCssInputs(directory) {
    if (!existsSync(directory)) {
        return [];
    }

    return readdirSync(directory).flatMap((entry) => {
        const fullPath = join(directory, entry);

        if (statSync(fullPath).isDirectory()) {
            return collectCssInputs(fullPath);
        }

        return fullPath.endsWith('.css') ? fullPath.replaceAll('\\', '/') : [];
    });
}

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                ...collectCssInputs('resources/css/views'),
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
