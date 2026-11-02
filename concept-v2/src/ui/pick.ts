import gsap from 'gsap'
import { state } from '../state'
import type { Stage } from '../gl/stage'

/**
 * The live thing. Choose a letter and it steps forward and says whose it is.
 * Without script both halves are simply listed; with it, one shows at a time.
 */
export function initPick(_stage: Stage | null) {
  const buttons = [...document.querySelectorAll<HTMLButtonElement>('.pick__btn')]
  const edges = [...document.querySelectorAll<HTMLButtonElement>('.edge')]
  const halves = [...document.querySelectorAll<HTMLElement>('.half')]
  const statement = document.querySelector<HTMLElement>('.letters__statement')
  const wrap = document.querySelector<HTMLElement>('.halves')
  const narrow = matchMedia('(max-width: 899px)')
  let current: 'A' | 'K' | null = null

  const choose = (id: 'A' | 'K' | null) => {
    current = id
    for (const b of buttons) b.setAttribute('aria-pressed', String(b.dataset.pick === id))
    for (const h of halves) h.classList.toggle('is-open', h.dataset.half === id)
    if (wrap) {
      if (id) wrap.dataset.active = id
      else delete wrap.dataset.active
    }
    // On a narrow screen the chosen half takes the statement's place.
    if (statement) statement.hidden = narrow.matches && id !== null
    gsap.to(state, { pick: id === 'A' ? -1 : id === 'K' ? 1 : 0, duration: 0.9, ease: 'expo.out', overwrite: 'auto' })
    const open = halves.find((h) => h.dataset.half === id)
    if (open && narrow.matches) gsap.fromTo(open, { opacity: 0, y: 16 }, { opacity: 1, y: 0, duration: 0.5, ease: 'power3.out', clearProps: 'opacity,transform' })
  }

  const toggle = (id: 'A' | 'K') => choose(current === id ? null : id)
  for (const b of buttons) b.addEventListener('click', () => toggle(b.dataset.pick as 'A' | 'K'))
  for (const e of edges) e.addEventListener('click', () => toggle(e.dataset.pick as 'A' | 'K'))
  // Hovering a letter at the edge previews it, the way a hand reaches for one.
  for (const e of edges) {
    e.addEventListener('pointerenter', () => {
      if (current === null) gsap.to(state, { pick: e.dataset.pick === 'A' ? -0.25 : 0.25, duration: 0.6, ease: 'power3.out', overwrite: 'auto' })
    })
    e.addEventListener('pointerleave', () => {
      if (current === null) gsap.to(state, { pick: 0, duration: 0.6, ease: 'power3.out', overwrite: 'auto' })
    })
  }
}
