/** @type {import('tailwindcss').Config} */

export default {
  content: [
    "./resources/views/**/*.blade.php",
    "./resources/js/**/*.js",
    "./resources/js/**/*.vue",
    "./resources/components/**/*.blade.php",
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: [
          "Inter",
          "system-ui",
          "-apple-system",
          "Segoe UI",
          "Roboto",
          "Helvetica Neue",
          "Arial",
        ],

        serif: ["Georgia", "Cambria", "Times New Roman", "Times"],

        mono: [
          "JetBrains Mono",
          "Fira Code",
          "Menlo",
          "Monaco",
          "Consolas",
          "Courier New",
        ],
      },
    },
  },

  plugins: [],

  corePlugins: {
    preflight: true,
  },
};
