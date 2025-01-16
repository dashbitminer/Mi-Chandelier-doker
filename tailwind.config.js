import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import flowbite from 'flowbite/plugin';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        'node_modules/flowbite-vue/**/*.{js,jsx,ts,tsx,vue}',
        'node_modules/flowbite/**/*.{js,jsx,ts,tsx}'
    ],

    theme: {
        fontFamily: {
            sans: ['Poppins'],
        },
        screens: {
            'xs': '475px',
            '2xs': '300px',
            ...defaultTheme.screens
        },
        extend: {
            colors: {
                // Shade palette based on Glasswing primary color "#0097A0"
                primary: {
                    100: '#e6f5f6',
                    200: '#b3e0e3',
                    300: '#80cbd0',
                    400: '#4db6bd',
                    500: '#0097A0',
                    600: '#007980',
                    700: '#005b60',
                }
            }
        }
    },

    plugins: [forms, typography, flowbite],
};
