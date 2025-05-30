// tailwind.config.js

import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import flowbitePlugin from 'flowbite/plugin'; // For Flowbite's Tailwind plugin

/** @type {import('tailwindcss').Config} */
export default {
    // Enable class-based dark mode
    // This means Tailwind will apply dark mode styles when <html class="dark">
    darkMode: 'class',

    content: [
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
        './resources/views/**/*.blade.php',

        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',

        // Paths for Flowbite and Flowbite-Vue components
        'node_modules/flowbite-vue/**/*.{js,jsx,ts,tsx}',
        'node_modules/flowbite/**/*.{js,jsx,ts,tsx}',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            // You can extend your theme with specific dark mode colors here if needed,
            // although using `dark:` prefixes directly in your components is often more straightforward.
            // Example:
            // colors: {
            //   brand: {
            //     light: '#3490dc',
            //     dark: '#2779bd', // A darker shade for dark mode
            //   },
            //   background: {
            //      DEFAULT: '#FFFFFF',
            //      dark: '#111827', // e.g. gray-900
            //   }
            // }
        },
    },

    plugins: [
        forms,
        flowbitePlugin, // Add the Flowbite plugin
    ],
};
