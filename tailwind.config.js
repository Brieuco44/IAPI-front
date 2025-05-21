/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './templates/**/*.html.twig', // Parcourez vos fichiers Twig pour détecter les classes CSS
    './assets/**/*.js', // Inclure également vos fichiers JS
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require("daisyui"),
    require("tailwindcss"),
    require("autoprefixer"),
  ],
  themes: ["dark"]
}

