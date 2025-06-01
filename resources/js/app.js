import 'flowbite'; // For Flowbite's JS components
import '../css/app.css'; // Your main application CSS
import './bootstrap'; // Laravel's bootstrap.js (Axios, Echo, etc.)
import 'vue-toast-notification/dist/theme-default.css'; // Toast styles

import { createApp, h } from 'vue';
import { createInertiaApp, Link, Head } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js';
import ToastPlugin from 'vue-toast-notification';
import i18n from './i18n'; // vue-i18n plugin

const appName = import.meta.env.VITE_APP_NAME || 'AppSchool';

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) =>
    resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) });

    // Register global components
    app.component('Link', Link);
    app.component('Head', Head);

    // Use Vue plugins
    app.use(plugin);
    app.use(ZiggyVue, props.initialPage.props.ziggy);
    app.use(i18n); //  vue-i18n
    app.use(ToastPlugin); // Toast notifications

    app.mount(el);
  },
  progress: {
    color: '#33cbff',
    showSpinner: true,
  },
});
