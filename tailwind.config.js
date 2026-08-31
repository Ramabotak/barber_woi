import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
  extend: {
    colors: {
      charcoal: '#1C1C1E',
      gold: '#C9A24B',
      cream: '#F7F5F1',
      brandsuccess: '#2E7D32',
      brandwarning: '#B8860B',
      branddanger: '#C0392B',
      muted: '#8A8A8E',
    },
    fontFamily: {
      heading: ['"Plus Jakarta Sans"', 'sans-serif'],
      body: ['Inter', 'sans-serif'],
    },
  },
},

    plugins: [forms],
};
