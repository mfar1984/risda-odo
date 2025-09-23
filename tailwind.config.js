import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                'xs': ['10px', '14px'],
                'sm': ['12px', '16px'],
                'base': ['14px', '20px'],
            },
            borderRadius: {
                'none': '0px',
                'sm': '1px',
                'DEFAULT': '2px',
                'md': '2px',
                'lg': '2px',
                'xl': '2px',
                '2xl': '2px',
                '3xl': '2px',
                'full': '9999px',
            },
            colors: {
                'risda': {
                    'primary': '#1e40af',
                    'secondary': '#64748b',
                    'success': '#059669',
                    'warning': '#d97706',
                    'danger': '#dc2626',
                    'info': '#0284c7',
                },
            },
        },
    },

    plugins: [forms],
};
