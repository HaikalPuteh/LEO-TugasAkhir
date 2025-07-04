// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/Earth3Dsimulation.js',
        'resources/js/Earth2Dsimulation.js',
      ],
      refresh: true,
    }),
  ],
  resolve: {
    alias: {
      'three': path.resolve(__dirname, 'node_modules/three'),
      'three/examples/jsm': path.resolve(__dirname, 'node_modules/three/examples/jsm'),
      'satellite.js': path.resolve(__dirname, 'node_modules/satellite.js/dist/satellite.es.js'),
      'd3': path.resolve(__dirname, 'node_modules/d3'),
    },
  },
  optimizeDeps: {
    include: ['satellite.js'],
  },
});
