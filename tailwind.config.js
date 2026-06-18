import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import rtl from 'tailwindcss-rtl';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/View/Components/**/*.php',
        './app/Http/Controllers/**/*.php',
    ],

    theme: {
        extend: {
            colors: {
                brand: {
                    50:  '#fff7ed',
                    100: '#ffedd5',
                    200: '#fed7aa',
                    300: '#fdba74',
                    400: '#fb923c',
                    500: '#f97316',
                    600: '#ea580c',
                    700: '#c2410c',
                    800: '#9a3412',
                    900: '#7c2d12',
                },
                sand: {
                    50:  '#fdfaf6',
                    100: '#f7efe3',
                    200: '#efdcc2',
                    300: '#e3c39a',
                    400: '#d4a574',
                    500: '#b8865a',
                    600: '#946a47',
                    700: '#75543a',
                    800: '#5b4231',
                    900: '#3f2e22',
                },
            },
            fontFamily: {
                sans: ['Figtree', 'Inter', ...defaultTheme.fontFamily.sans],
                display: ['"Playfair Display"', 'serif'],
                arabic: ['"Noto Kufi Arabic"', 'sans-serif'],
            },
            container: {
                center: true,
                padding: {
                    DEFAULT: '1rem',
                    sm: '1.5rem',
                    lg: '2rem',
                },
            },
            boxShadow: {
                card: '0 4px 16px -2px rgba(15, 23, 42, 0.08)',
                'card-hover': '0 12px 28px -8px rgba(15, 23, 42, 0.18)',
            },
            animation: {
                'fade-in': 'fadeIn .25s ease-out',
            },
            keyframes: {
                fadeIn: { '0%': { opacity: 0 }, '100%': { opacity: 1 } },
            },
        },
    },

    plugins: [forms, typography, rtl],
};
