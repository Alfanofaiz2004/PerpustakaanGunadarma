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
                "gunadarma-navy": "#0284C7",
                "gunadarma-navy-hover": "#0369A1",
                "sky-brand": "#0284C7",
                "sky-hover": "#0369A1",
                "sky-subtle": "#F0F9FF",
                "sky-border": "#E0F2FE",
                "stock-available": "#10B981",
                "stock-available-bg": "#ECFDF5",
                "stock-empty": "#EF4444",
                "stock-empty-bg": "#FEF2F2",
                "status-borrowed": "#F59E0B",
                "status-borrowed-bg": "#FFFBEB",
                "status-returned": "#0284C7",
                "status-returned-bg": "#F0F9FF",
                "border-subtle": "#E2E8F0",
                "border-strong": "#CBD5E1",
                "surface-canvas": "#F8FAFC",
                "surface-card": "#FFFFFF",
                "surface-container": "#e0f2fe",
                "surface-container-low": "#f0f9ff",
                "surface-container-high": "#bae6fd",
                "on-surface": "#0f172a",
                "on-surface-variant": "#475569",
                "primary": "#0284C7",
                "on-primary": "#ffffff",
                "error": "#ef4444",
                "error-container": "#fef2f2",
                "outline": "#64748b",
            },
            fontFamily: {
                sans: ['"Open Sans"', ...defaultTheme.fontFamily.sans],
                heading: ['"Open Sans"', ...defaultTheme.fontFamily.sans],
                mono: ['ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'Consolas', 'monospace'],
            },
        },
    },

    plugins: [forms],
};
