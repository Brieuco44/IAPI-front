import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from "path";
import { VitePWA } from 'vite-plugin-pwa'


export default defineConfig({
  plugins: [
    vue(),
    VitePWA({
      devOptions: {
        enabled: false,
        type: 'module',
        /* other options */
      },
      manifest: {
        name: 'ChatHouse',
        short_name: 'ChatHouse',
        description: 'ChatHouse, une application de chat en ligne',
        theme_color: '#1D232A',
        background_color: '#ffffff', // Add background color for better PWA support
        display: 'standalone', // Improve performance by running standalone
        start_url: '/', // Ensure the PWA starts at the root
        scope: '/', // Define the scope for the PWA
        icons: [
          {
            src: '/icons/icon-192x192.png',
            sizes: '192x192',
            type: 'image/png',
          },
          {
            src: '/icons/icon-512x512.png',
            sizes: '512x512',
            type: 'image/png',
          },
        ],
      },
      strategies: 'generateSW', // Use the generateSW strategy for optimal caching
      workbox: {
        runtimeCaching: [
          {
            urlPattern: /\/index\.html$/, // Assurez-vous de cacher l'index
            handler: 'StaleWhileRevalidate', // Fournit une copie du cache si possible
            options: {
              cacheName: 'html-cache',
            },
          },
          {
            urlPattern: /\.(?:js|css)$/, // Cache JS et CSS
            handler: 'StaleWhileRevalidate',
            options: {
              cacheName: 'static-resources',
            },
          },
          {
            urlPattern: /\.(?:png|jpg|jpeg|svg|gif|ico)$/, // Cache les images
            handler: 'CacheFirst',
            options: {
              cacheName: 'image-cache',
              expiration: {
                maxEntries: 100,
                maxAgeSeconds: 60 * 60 * 24 * 30, // 30 jours
              },
            },
          },
        ],
      },
    })
  ],
  build: {
    rollupOptions: {
      output: {
        entryFileNames: 'assets/[name].[hash].js', // Customize JS file naming
        chunkFileNames: 'assets/[name].[hash].js', // Customize chunk naming
        assetFileNames: 'assets/[name].[hash].[ext]', // Customize asset naming
      },
    },
    outDir: 'dist', // Ensure the output directory is set to 'dist'
    emptyOutDir: true, // Clears the directory before building
  },
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./src"), // Ensure this matches your source directory
    },
  },
})
