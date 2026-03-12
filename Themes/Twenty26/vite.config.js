import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  build: {
    outDir: 'dist',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        modern: resolve(__dirname, 'src/js/main.js'),
      },
      output: {
        entryFileNames: '[name].min.js',
        assetFileNames: '[name].min[extname]',
      },
    },
    cssMinify: true,
    minify: 'terser',
    sourcemap: true,
  },
  css: {
    postcss: './postcss.config.js',
  },
});
