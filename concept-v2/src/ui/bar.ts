/** The bar: solid once the page moves, out of the way while reading down. */
export function initBar() {
  const bar = document.querySelector<HTMLElement>('.bar')
  const btn = document.querySelector<HTMLButtonElement>('.bar__menu')
  const nav = document.querySelector<HTMLElement>('.bar__nav')
  if (!bar || !btn || !nav) return

  let last = scrollY
  addEventListener('scroll', () => {
    const y = scrollY
    bar.classList.toggle('is-solid', y > 40)
    if (!bar.classList.contains('is-open')) bar.classList.toggle('is-hidden', y > last && y > innerHeight * 0.6)
    last = y
  }, { passive: true })

  const close = () => {
    bar.classList.remove('is-open')
    btn.setAttribute('aria-expanded', 'false')
    btn.textContent = 'Menu'
  }
  btn.addEventListener('click', () => {
    const open = !bar.classList.contains('is-open')
    bar.classList.toggle('is-open', open)
    btn.setAttribute('aria-expanded', String(open))
    btn.textContent = open ? 'Close' : 'Menu'
    if (open) nav.querySelector<HTMLAnchorElement>('a')?.focus()
  })
  nav.addEventListener('click', (e) => {
    if ((e.target as HTMLElement).closest('a')) close()
  })
  addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && bar.classList.contains('is-open')) {
      close()
      btn.focus()
    }
  })
}
