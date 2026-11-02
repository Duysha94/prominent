import '@fontsource-variable/epilogue'
import './styles.css'
import { boot } from './app'

boot().catch((err) => {
  // Never leave content hidden behind an entrance that will not run.
  console.error(err)
  document.documentElement.classList.remove('motion', 'gl')
})
