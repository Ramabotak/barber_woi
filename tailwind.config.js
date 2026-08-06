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
            colors: {
                primary: '#002045',
                'primary-container': '#1a365d',
                secondary: '#735c00',
                'secondary-container': '#fed65b',
                
                background: '#f7fafc',
                surface: '#f7fafc',
                'surface-bright': '#f7fafc',
                'surface-container-lowest': '#ffffff',
                'surface-container-low': '#f1f4f6',
                'surface-container': '#ebeef0',
                'surface-container-high': '#e5e9eb',
                'on-surface': '#181c1e',
                'on-surface-variant': '#43474e',
                outline: '#74777f',
                'outline-variant': '#c4c6cf',
                'on-primary': '#ffffff',
                'on-secondary': '#ffffff',
                error: '#ba1a1a',
                  'brand-gold': '#D4AF37',   
                    'brand-navy': '#1A365D',  
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                body: ['Inter', 'sans-serif'],
                display: ['Inter', 'sans-serif'],
            },
            fontSize: {
                'display-lg': ['48px', { lineHeight: '56px', fontWeight: '700', letterSpacing: '-0.02em' }],
                'headline-lg': ['32px', { lineHeight: '40px', fontWeight: '600', letterSpacing: '-0.01em' }],
                'headline-md': ['24px', { lineHeight: '32px', fontWeight: '600' }],
                'headline-sm': ['20px', { lineHeight: '28px', fontWeight: '600' }],
                'body-lg': ['18px', { lineHeight: '28px' }],
                'body-md': ['16px', { lineHeight: '24px' }],
                'body-sm': ['14px', { lineHeight: '20px' }],
                'label-md': ['12px', { lineHeight: '16px', letterSpacing: '0.05em', fontWeight: '600' }],
            },
            spacing: {
                'stack-sm': '8px',
                'stack-md': '16px',
                'stack-lg': '32px',
                'margin-mobile': '20px',
            },
            borderRadius: {
                DEFAULT: '0.125rem',
                lg: '0.25rem',
                xl: '0.5rem',
                full: '0.75rem',
            },
        },
    },

    plugins: [forms],
};
