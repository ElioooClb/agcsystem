import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/chantier/workSite.js",
                "resources/js/chantier/create.js",
                "resources/js/heures/chantier.js",
                "resources/js/heures/declarations.js",
                "resources/js/heures/adminDeclaration.js",
                "resources/js/heures/scheduleModalHandler.js",
                "resources/js/heures/worksiteState.js",
                "resources/js/heures/deleteWorksite.js",
                "resources/js/emails/email-settings.js",
                "resources/js/parameters/manage.js",
                'resources/js/users/coefProd.js',
                'resources/js/chantier/statistics.js',
            ],
            resolve: {
                alias: {
                    "@": "/resources/js",
                },
            },
            refresh: true,
        }),
    ],
    // DEBUT [SPECGT29] - Importation du plugin cssnano pour la minification des fichiers CSS et configuration de vite
    css: {
        postcss: {
            plugins: [
                require("tailwindcss"),
                require("autoprefixer"),
                require("cssnano"),
            ],
        },
    },
    build: {
        outDir: "public/build", // Répertoire de sortie des assets générés
        minify: true, // Activer la minification des fichiers JavaScript et CSS
    },
});
// FIN [SPECGT29] - Importation du plugin cssnano pour la minification des fichiers CSS et configuration de vite
