import React from "react";
import { createRoot } from "react-dom/client";
import { createInertiaApp } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";

import Layout from "./Layouts/AppLayout";
import ThemeProvider from "@/Providers/ThemeProvider";

createInertiaApp({
    resolve: async (name) => {
        const page = await resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob("./Pages/**/*.jsx"),
        );

        page.default.layout ??= (page) => <Layout>{page}</Layout>;

        return page;
    },

    defaults: {
        visitOptions: (href, options) => {
            return { viewTransition: true };
        },
    },

    setup({ el, App, props }) {
        createRoot(el).render(
            <React.StrictMode>
                <ThemeProvider>
                    <App {...props} />
                </ThemeProvider>
            </React.StrictMode>,
        );
    },
});
