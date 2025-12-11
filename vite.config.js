import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  plugins: [vue()],
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

