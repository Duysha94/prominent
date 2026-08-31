/**
 * Build-time entry for scripts/prerender.mjs.
 *
 * The prerenderer runs in plain Node after `vite build`, so it cannot import
 * the TypeScript sources directly. esbuild bundles this one file into
 * `.seo-build/seo.js`, which keeps the page metadata and the JSON-LD graph in
 * a single place — the same modules the React app uses — rather than
 * duplicating them in a build script where they would quietly drift.
 */
export { PAGES, siteGraph, breadcrumbFor } from '../src/data/seo'
export { STUDIO } from '../src/data/studio'
