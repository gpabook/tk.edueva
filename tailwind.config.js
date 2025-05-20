import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
        './resources/views/**/*.blade.php',

        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',

             // Flowbite-Vue & core Flowbite
        'node_modules/flowbite-vue/**/*.{js,jsx,ts,tsx}',
        'node_modules/flowbite/**/*.{js,jsx,ts,tsx}',



    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        forms,
              // This is the Tailwind plugin that powers Flowbite’s interactive components
        require('flowbite/plugin'),
    ],
};
