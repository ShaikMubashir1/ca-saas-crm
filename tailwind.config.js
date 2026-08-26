import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/views/livewire/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/**/*.php',
        './app/**/*.php',
        './routes/**/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    red: '#ED1C24',
                    'red-dark': '#C9141B',
                    'red-light': '#FDF2F2',
                    charcoal: '#252525',
                    dark: '#1E1E1E',
                    bg: '#F7F7F8',
                    border: '#E5E5E5',
                    muted: '#737373',
                }
            }
        },
    },

    plugins: [forms],
};
