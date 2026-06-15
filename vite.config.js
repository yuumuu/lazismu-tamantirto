import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import { svelte } from "@sveltejs/vite-plugin-svelte";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/css/guest.css",
                "resources/js/guest.js",
            ],
            refresh: true,
        }),
        tailwindcss(),
        svelte(),
    ],
    optimizeDeps: {
        exclude: ["@inertiajs/svelte", "lucide-svelte"],
    },
    server: {
        cors: true,
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
    },
});
