import 'flowbite'; // For Flowbite's JS components
import '../css/app.css'; // Your main application CSS
import './bootstrap'; // Laravel's bootstrap.js (Axios, Echo, etc.)
import 'vue-toast-notification/dist/theme-default.css'; // CSS for toast notifications

import { createApp, h } from 'vue';
import { createInertiaApp, Link, Head, usePage } from '@inertiajs/vue3'; // Ensure Link and Head are imported here
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js'; // For Laravel named routes in JS
import ToastPlugin from 'vue-toast-notification'; // For toast notifications

const appName = import.meta.env.VITE_APP_NAME || 'AppSchool';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        // Create the Vue app instance
        const appInstance = createApp({ render: () => h(App, props) });

        // Use plugins
        appInstance.use(plugin);
        appInstance.use(ZiggyVue, props.initialPage.props.ziggy); // Pass Ziggy config from initial props
        appInstance.use(ToastPlugin);

        // --- Language Configuration START ---
        // Global translation helper function
        appInstance.config.globalProperties.$t = (key, replacements = {}) => {
            const page = usePage(); // Get current page's props reactively
            const translations = page.props.translations || {}; // Ensure translations is an object
            let translation = translations[key] || key;
            Object.keys(replacements).forEach(rKey => {
                translation = translation.replace(`:${rKey}`, replacements[rKey]);
            });
            return translation;
        };

        // Optional: Global helper to get current locale if needed directly in templates
        appInstance.config.globalProperties.$currentLocale = () => {
            const page = usePage();
            return page.props.locale;
        };
        // --- Language Configuration END ---

        // Register Inertia components globally (optional, can also import per component)
        appInstance.component('Link', Link);
        appInstance.component('Head', Head);

        // Mount the app
        return appInstance.mount(el);
    },
    progress: {
        color: '#33cbff', // Custom progress bar color
        showSpinner: true // Show spinner with progress bar
    },
});
