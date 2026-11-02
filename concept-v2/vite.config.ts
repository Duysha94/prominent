import { defineConfig } from 'vite'

// Relative base: the build runs from any sub-path (GitHub Pages, an artifact,
// or a WordPress theme folder) without rewriting URLs.
export default defineConfig({
  base: './',
  server: {
    // NO_HMR=1 for the offline film renders: an edit elsewhere must not reload them.
    hmr: !process.env.NO_HMR,
    watch: process.env.NO_HMR ? { ignored: ['**/*'] } : undefined,
  },
  build: {
    target: 'es2022',
    assetsInlineLimit: 0,
    chunkSizeWarningLimit: 900,
  },
})
