/**
 * Primary navigation.
 *
 * Lives here rather than in Header.tsx because the overlay menu and the route
 * curtain both need it, and importing page data out of a component makes the
 * dependency read backwards.
 */
export const NAV = [
  { to: '/work', label: 'Work', index: '01' },
  { to: '/services', label: 'Services', index: '02' },
  { to: '/studio', label: 'Studio', index: '03' },
  { to: '/journal', label: 'Journal', index: '04' },
] as const
