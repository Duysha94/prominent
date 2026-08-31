/** Tiny class joiner. No need for clsx-scale dependencies here. */
export const cn = (...parts: Array<string | false | null | undefined>) =>
  parts.filter(Boolean).join(' ')
