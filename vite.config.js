import { fileURLToPath, URL } from 'node:url'; // Required for aliases
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import svgLoader from 'vite-svg-loader'; // Import the loader

export default defineConfig({
  plugins: [
    vue(), 
    svgLoader() // Enable SVG component support
  ],
  resolve: {
    alias: {
      // Maps '@' to the root directory so Vite can find your assets
      '@': fileURLToPath(new URL('./', import.meta.url))
    }
  },
  define: {
    'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV || 'production'),
  },
  build: {
    outDir: 'assets/dist',
    emptyOutDir: false,
    lib: {
      entry: 'assets/js/frontend/main.js',
      name: 'WiFormFrontend',
      formats: ['iife'],
      fileName: () => 'wiform-frontend.js',
    },
    rollupOptions: {
      output: {
        assetFileNames: () => 'wiform-frontend.css',
      },
    },
  },
});