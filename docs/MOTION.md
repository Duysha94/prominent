# Motion system

One vocabulary, used by CSS, GSAP and Motion alike. If an animation is not
expressible in these tokens, the animation is wrong, not the tokens.

---

## Doctrine

Every animation on this site must be one of four things. Anything that is none
of them is deleted.

| Class | Job | Example here |
|---|---|---|
| **Feedback** | You did something | Button press, filter select, theme switch |
| **Orientation** | Where you are | The Seam's knot, header state, route curtain |
| **Continuity** | This thing became that thing | Work row → case study, shared-element morph |
| **Atmosphere** | The room is alive | Grain, the Seam's slack, the live marquee |

Three standing rules:

1. **One strong text animation per page, not a system of them.** Cut text is
   reserved for the page's primary headline and section openers. Everything
   else arrives by weight or by opacity.
2. **Motion clarifies hierarchy; it never carries information alone.** Any
   figure revealed by motion is also in the DOM without it.
3. **Nothing animates a layout property.** `transform` and `opacity` only,
   with `clip-path` used sparingly and deliberately.

---

## Two tiers

```
Tier 1  CSS scroll-driven animation   compositor thread, 0KB JS, cannot touch INP
Tier 2  GSAP                          lazily loaded, only where CSS cannot go
```

The CSS scroll-driven spec deliberately excludes pinning and programmatic
scrub. That exclusion is the tier boundary — if a thing can be done with
`animation-timeline`, it must be.

Everything in tier 1 is written as:

```css
/* finished state is the default */
.thing { opacity: 1; transform: none; }

@supports (animation-timeline: view()) {
  @media (prefers-reduced-motion: no-preference) {
    .thing {
      animation: reveal linear both;
      animation-timeline: view();
      animation-range: entry 0% entry 70%;
    }
  }
}
```

so a browser without scroll timelines gets the finished page, not a blank one.

---

## Tokens

### Easing

| Token | Curve | Use |
|---|---|---|
| `--ease-cut` | `cubic-bezier(0.16, 1, 0.30, 1)` | Reveals, arrivals. Fast out, long tail |
| `--ease-thread` | `cubic-bezier(0.65, 0.05, 0.36, 1)` | Things that travel rather than arrive — the Seam, the curtain |
| `--ease-snap` | `cubic-bezier(0.34, 1.56, 0.64, 1)` | Measurement pins landing. Never on large objects |
| `--ease-drape` | `cubic-bezier(0.22, 0.61, 0.36, 1)` | Soft settle, no overshoot. Theme swap, crossfades |

Two springs are **sampled from real physics** into CSS `linear()` and exported
as Motion configs from the same numbers, so a CSS transition and a JS animation
on the same element are identical:

| Token | Physics | Settles |
|---|---|---|
| `--ease-spring-ui` | stiffness 320, damping 26, mass 1 | 531ms |
| `--ease-spring-panel` | stiffness 200, damping 24, mass 1 | 576ms |

Generic `ease-in-out` is banned from the codebase.

### Duration

| Token | Value | Use |
|---|---|---|
| `--duration-instant` | 120ms | Hover colour, focus ring |
| `--duration-quick` | 240ms | Small state change |
| `--duration-base` | 480ms | Panels, curtains, callouts |
| `--duration-slow` | 820ms | Section reveals |
| `--duration-reveal` | 1200ms | Headline cut |

### Stagger

| Token | Value |
|---|---|
| `char` | 6ms |
| `word` | 14ms |
| `line` | 28ms |
| `card` | 60ms |
| `block` | 80ms |

**Hard cap: total stagger stays under 500ms.** For a list of `n`, per-item
delay is `min(base, 500 / n)`. A reveal that becomes a queue has failed.

### Travel

`hair 6px · short 18px · medium 40px · long 88px`. Reveals travel a short,
consistent distance — the drama comes from the easing tail, not the distance.

---

## The signature move

Headline weight and optical size as a function of scroll position, in pure CSS:

```css
.ak-vf {
  animation: ak-weight linear both;
  animation-timeline: view();
  animation-range: entry 10% cover 45%;
}
@keyframes ak-weight {
  from { font-variation-settings: "wght" 300, "opsz" 14,  "SOFT" 0, "WONK" 1; }
  to   { font-variation-settings: "wght" 700, "opsz" 144, "SOFT" 0, "WONK" 1; }
}
```

Nothing moves and nothing fades — the type gains weight as it travels up the
viewport. Only `wght` and `opsz` are animated, never `wdth`: width changes the
advance and would reflow the line mid-scroll. The figures in the readout use
Martian Mono's `wdth` axis instead, where a fixed-width column absorbs it.

Zero JavaScript, compositor-thread, and it degrades to static type.

---

## Reduced motion

A **designed mode**, not `animation: none`.

| Full | Reduced |
|---|---|
| Cut text reveal | Text simply present, never split |
| Scroll reveals | Final state at first paint |
| The Seam bows with scroll velocity | Straight thread, knot still tracks position |
| Marquee scrolls | Frozen, content still readable |
| Loader runs | Skipped entirely |
| Lenis smooth scroll | Disabled — native scrolling |
| Variable-font scroll weight | Static at the finished weight |

The rule: **remove the travel, keep the information.** A reduced-motion visitor
must never end up with less content, and never with a fade that did not finish.

Also honoured: `prefers-reduced-transparency` (grain removed) and
`forced-colors`.

---

## Where each token is defined

| Layer | File |
|---|---|
| CSS custom properties | `src/styles/tokens.css` |
| JS/TS constants (Motion, GSAP) | `src/lib/motion.ts` |
| Tier 1 keyframes | `src/styles/base.css` |
| GSAP registration + named eases | `src/lib/gsap.ts` |

`src/lib/gsap.ts` registers the four curves as GSAP `CustomEase`s with the same
control points as the CSS tokens. There is one source of truth for the motion,
in two syntaxes.
