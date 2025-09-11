/** @type {import('tailwindcss').Config} */
const {blackA, green, grass, mauve} = require('@radix-ui/colors')
export default {
    content: ['./resources/**/*.blade.php', './resources/**/*.vue', './resources/**/*.js', './node_modules/reka-ui/**/*.{js,ts,vue}',],
    theme: {
        extend: {
            fontFamily: {
                poppins: ['Poppins', 'sans-serif'],
            },
            colors: {
                'primary-strong': '#c8a02e',
            },
            keyframes: {},
            animation: {},
        },
    },
    plugins: [],
    corePlugins: {
        strokeWidth: true,
    },
    safelist: [{
        pattern: /data-\[.*\]/,
    },],
};
