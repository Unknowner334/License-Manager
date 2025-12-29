/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    './resources/views/**/*.blade.php',
  ],
  safeList: [
    'text-red-600',
    'text-green-600',
    'text-yellow-300',
  ],
  theme: {
    extend: {
      colors: {
        primary: '#0d6efd',
        secondary: {
          DEFAULT: '#6c757d',
          lighter: 'rgb(226, 227, 229)',
          darker: '#55595c',
        },
        dark: {
          DEFAULT: 'oklch(27.4% 0.006 286.033)',
          border: '#e6e6e6',
          text: '#666666',
        },
        light: {
          DEFAULT: 'whitesmoke',
        },
      },
      blur: {
        DEFAULT: '3px',
        none: '0px',
      },
      spacing: {
        128: '32rem',
      },
      fontFamily: {
        sans: ['Poppins', 'sans-serif'],
      },
    },
  },
  plugins: [require('tailwind-scrollbar')],
};
