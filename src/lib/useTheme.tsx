import { createContext, useCallback, useContext, useEffect, useState, type ReactNode } from 'react'

/**
 * ATELIER (light) and RUNWAY (dark) are two designed rooms, not one palette
 * inverted — see base.css. This provider only owns *which* room you are in.
 */
export type Theme = 'light' | 'dark'

const KEY = 'ak-theme'

/** Read the choice the inline boot script already applied, so we never flash. */
const readApplied = (): Theme =>
  (document.documentElement.getAttribute('data-theme') as Theme | null) ??
  (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')

const ThemeCtx = createContext<{ theme: Theme; toggle: () => void; set: (t: Theme) => void }>({
  theme: 'dark',
  toggle: () => {},
  set: () => {},
})

export function ThemeProvider({ children }: { children: ReactNode }) {
  const [theme, setTheme] = useState<Theme>(readApplied)

  useEffect(() => {
    document.documentElement.setAttribute('data-theme', theme)
    try {
      localStorage.setItem(KEY, theme)
    } catch {
      // Private mode / blocked storage: the choice simply lasts this visit.
    }
  }, [theme])

  // Follow the OS only while the visitor has not expressed a preference.
  useEffect(() => {
    let stored: string | null = null
    try {
      stored = localStorage.getItem(KEY)
    } catch {
      /* ignore */
    }
    if (stored) return
    const mq = window.matchMedia('(prefers-color-scheme: dark)')
    const onChange = (e: MediaQueryListEvent) => setTheme(e.matches ? 'dark' : 'light')
    mq.addEventListener('change', onChange)
    return () => mq.removeEventListener('change', onChange)
  }, [])

  const toggle = useCallback(() => setTheme((t) => (t === 'dark' ? 'light' : 'dark')), [])

  return <ThemeCtx.Provider value={{ theme, toggle, set: setTheme }}>{children}</ThemeCtx.Provider>
}

export const useTheme = () => useContext(ThemeCtx)
