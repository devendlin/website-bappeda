/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    "./app/Views/**/*.php",
  ],
  theme: {
    extend: {
      animation: {
        stack: 'stackFade 12s infinite',
      },
      keyframes: {
        stackFade: {
          '0%':   { opacity: '0', transform: 'scale(0.96) translateY(20px)' },
          '10%':  { opacity: '1', transform: 'scale(1) translateY(0)' },
          '30%':  { opacity: '1', transform: 'scale(1) translateY(0)' },
          '40%':  { opacity: '0', transform: 'scale(1.02) translateY(-20px)' },
          '100%': { opacity: '0' },
        }
      }
    }
  },
  plugins: [
    require('@tailwindcss/line-clamp'),
  ],
}
