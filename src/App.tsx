import { Suspense, lazy, useCallback, useState } from 'react'
import { Routes, Route } from 'react-router-dom'
import { Header } from './components/shell/Header'
import { MenuOverlay } from './components/shell/MenuOverlay'
import { Footer } from './components/shell/Footer'
import { Seam } from './components/shell/Seam'
import { Loader } from './components/shell/Loader'
import { RouteCurtain, PageShell } from './components/shell/PageTransition'
import { Grain } from './components/primitives/Grain'
import { useLenis } from './lib/useLenis'
import { Home } from './pages/Home'

/**
 * Home ships in the entry chunk because it is the landing page and a lazy
 * boundary there would only add a waterfall. Every other route is split: a
 * visitor who never opens /contact never downloads the brief instrument.
 *
 * The Suspense fallback is deliberately empty rather than a spinner — the
 * route curtain is already covering the viewport during the swap, so a
 * spinner would flash underneath something opaque.
 */
const Work = lazy(() => import('./pages/Work').then((m) => ({ default: m.Work })))
const CaseStudyPage = lazy(() =>
  import('./pages/CaseStudy').then((m) => ({ default: m.CaseStudyPage })),
)
const Services = lazy(() => import('./pages/Services').then((m) => ({ default: m.Services })))
const Studio = lazy(() => import('./pages/Studio').then((m) => ({ default: m.Studio })))
const Journal = lazy(() => import('./pages/Journal').then((m) => ({ default: m.Journal })))
const Contact = lazy(() => import('./pages/Contact').then((m) => ({ default: m.Contact })))
const NotFound = lazy(() => import('./pages/NotFound').then((m) => ({ default: m.NotFound })))

export function App() {
  const [loading, setLoading] = useState(true)
  const [menuOpen, setMenuOpen] = useState(false)
  useLenis()

  const finish = useCallback(() => setLoading(false), [])

  return (
    <>
      {loading && <Loader onDone={finish} />}

      <a className="skip-link" href="#main">
        Skip to content
      </a>

      <Grain />
      <Seam />
      <Header onOpenMenu={() => setMenuOpen(true)} />
      <MenuOverlay open={menuOpen} onClose={() => setMenuOpen(false)} />
      <RouteCurtain />

      <PageShell>
        <Suspense fallback={<div className="min-h-[70svh]" />}>
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/work" element={<Work />} />
            <Route path="/work/:slug" element={<CaseStudyPage />} />
            <Route path="/services" element={<Services />} />
            <Route path="/studio" element={<Studio />} />
            <Route path="/journal" element={<Journal />} />
            <Route path="/contact" element={<Contact />} />
            <Route path="*" element={<NotFound />} />
          </Routes>
        </Suspense>
      </PageShell>

      <Footer />
    </>
  )
}
