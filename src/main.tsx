import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import { BrowserRouter } from 'react-router-dom'
// tokens.css pulls in base.css so both live in one Tailwind import graph.
import './styles/tokens.css'
import { App } from './App'
import { ThemeProvider } from './lib/useTheme'

createRoot(document.getElementById('root')!).render(
  <StrictMode>
    <BrowserRouter>
      <ThemeProvider>
        <App />
      </ThemeProvider>
    </BrowserRouter>
  </StrictMode>,
)
