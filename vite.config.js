import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/scss/app.scss",
                "resources/js/app.js",
                "resources/js/activity-logs.js",
                "resources/js/auth-login.js",
                "resources/js/charges.js",
                "resources/js/entities.js",
                "resources/js/interesados.js",
                "resources/js/legal-entities.js",
                "resources/js/natural-people.js",
                "resources/js/resolucions.js",
                "resources/js/roles.js",
                "resources/js/users.js",
            ],
            refresh: true,
        }),
    ],
    test: {
        environment: "jsdom",
        globals: true,
    },
});
