import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

// 1. Importaciones de PrimeVue (Diseño)
import PrimeVue from "primevue/config";
import Aura from "@primevue/themes/aura"; // El tema visual "Aura"
import "primeicons/primeicons.css"; // Iconos

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        return (
            createApp({ render: () => h(App, props) })
                .use(plugin)
                .use(ZiggyVue)
                // 2. Activamos PrimeVue con el tema Aura
                .use(PrimeVue, {
                    theme: {
                        preset: Aura,
                    },
                })
                .mount(el)
        );
    },
    progress: {
        color: "#4B5563",
    },
});
