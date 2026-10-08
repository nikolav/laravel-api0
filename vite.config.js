import { defineConfig } from "vite";
import laravelVitePlugin from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
  plugins: [
    laravelVitePlugin({
      input: ["resources/css/app.css", "resources/js/app.js"],
      refresh: true,
    }),
    tailwindcss(),
  ],
  server: {
    watch: {
      ignored: ["**/storage/framework/views/**"],
    },
  },
  resolve: {
    alias: {
      "@": "/resources/js",
      "@css": "/resources/css",
      "@images": "/resources/images",
    },
  },
});
