import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                headline: ['Manrope', 'sans-serif'],
            },

            colors: {
                primary: '#006191',
                secondary: '#d32f2f',
                tertiary: '#6366f1',

                surface: '#f8fafc',

                'surface-container': '#f1f5f9',
                'surface-container-low': '#f8fafc',
                'surface-container-lowest': '#ffffff',
                'surface-container-high': '#e2e8f0',

                'surface-variant': '#e2e8f0',

                'on-surface': '#0f172a',
                'on-surface-variant': '#64748b',

                outline: '#cbd5e1',
                'outline-variant': '#e2e8f0',

                'primary-container': '#dbeafe',

                error: '#dc2626',
                'error-container': '#fee2e2',
            },
        },
    },

    plugins: [forms],
};