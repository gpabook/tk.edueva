import 'flowbite';
import '../css/app.css';
import './bootstrap';
import 'vue-toast-notification/dist/theme-default.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
//import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { ZiggyVue } from 'ziggy-js';
import ToastPlugin from 'vue-toast-notification';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            //.use(ZiggyVue)
            .use(ZiggyVue, props.ziggy)
            .use(ToastPlugin)
            .mount(el);
    },
    progress: {
        //color: '#4B5563',
        color: '#33cbff',
        showSpinner: true
    },
});
