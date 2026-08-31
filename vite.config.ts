import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'
import { defineConfig } from 'vite'

export default defineConfig({
  plugins: [react(), tailwindcss()],
  build: {
    rollupOptions: {
      output: {
        // GSAP and Motion are the two heavy animation dependencies. Splitting
        // them keeps the entry chunk small enough that the hero headline — the
        // LCP element — never waits on animation code to parse.
        manualChunks(id) {
          if (id.includes('node_modules/gsap')) return 'gsap'
          if (id.includes('node_modules/motion') || id.includes('node_modules/framer-motion'))
            return 'motion'
          return undefined
        },
      },
    },
  },
})
