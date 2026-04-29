import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    safelist: [
        'from-tv-blue',
        'via-purple-600',
        'to-tv-pink',
        'bg-tv-blue',
        'bg-tv-pink',
        'bg-tv-bg',
        'text-tv-blue',
        'text-tv-pink',
        'text-tv-blue-dark',
        'border-tv-blue',
        'border-tv-pink',
        'shadow-tv-blue/20',
        'shadow-tv-pink/30',
        {
            pattern: /(bg|text|border|from|via|to)-tv-(blue|pink|bg|footer)(-dark|-light)?(\/\d+)?/,
        },
    ],
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                'tv-blue':       '#2227f5',
                'tv-blue-dark':  '#000ebf',
                'tv-pink':       '#e838bf',
                'tv-pink-light': '#fc59ce',
                'tv-bg':         '#f0f2ff',
                'tv-footer':     '#222222',
            },
            fontFamily: {
                sans:       ['Urbanist', ...defaultTheme.fontFamily.sans],
                urbanist:   ['Urbanist', ...defaultTheme.fontFamily.sans],
                montserrat: ['Montserrat', ...defaultTheme.fontFamily.sans],
                fira:       ['"Fira Sans"', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
