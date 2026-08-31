# AK Brand Development Studio — child theme for Zeyna

Installable WordPress child theme. It keeps Zeyna's header, footer, menu and
Barba page transitions, and layers the studio's design system and motion on
top.

---

## Install

1. WordPress admin → **Appearance → Themes → Add New → Upload Theme**
2. Upload `ak-zeyna-child.zip`
3. **Activate**

Zeyna must be installed (it does not need to be the active theme — a child
theme activates its parent automatically).

Then, in **Zeyna's theme options (Redux)**, make sure **Page transitions** is
**on**. Zeyna gates the whole Barba system on that switch, and the child theme
follows it rather than fighting it.

---

## What this actually gives you

| Included | Notes |
|---|---|
| The full colour system | `#f37021` with the measured contrast rules, both modes, wide-gamut P3 |
| Three variable fonts, self-hosted | Fraunces, Bricolage Grotesque, Martian Mono — subset and axis-pruned to 187KB total |
| **ATELIER / RUNWAY** light-dark mode | Zeyna has none — see below |
| The Seam | The orange thread, scroll-velocity reactive, in the footer so it survives navigations |
| Cut-text headline reveal | GSAP SplitText, dismantled after it settles so descenders are never clipped |
| Measure frames | Tech-pack annotations; hover, focus **and** touch |
| Tier-1 CSS motion | Scroll-driven reveals and variable-font weight, zero JavaScript |
| A Barba bridge | Teardown and rebuild around the container swap, plus focus and announcements |
| JSON-LD | Linked graph: the studio, both founders, and the platforms they founded |
| One page template | `AK — Bespoke page`, the pattern for all the others |

**Not included:** the finished page layouts. Those are a build project, not a
theme file — this gives you the system to build them in.

---

## Zeyna has no light/dark mode

Worth stating plainly because the brief assumed otherwise. Verified against the
theme source: there is not one occurrence of `data-theme`, `.dark`,
`.light-mode` or `prefers-color-scheme` in Zeyna's CSS, and no toggle in its
JavaScript. What looks like a dark mode in the demos is a **demo variant** — a
Redux colour preset you import once — not a switch a visitor can operate.

So the child theme brings its own, and it is better than a hex-swap: ATELIER
(the daylit workroom) and RUNWAY (the show) are two designed modes that differ
in *material* — paper fibre against film grain — not only in colour.

**Add the switch to the header** with either:

```php
<?php ak_mode_toggle(); ?>
```

…or the `[ak_mode_toggle]` shortcode if you are placing it from a builder.

The mode is resolved in a blocking inline script in `<head>` before first
paint, so a dark visitor never sees a white flash, and it is stored on
`<html data-theme>` rather than on `<body>` — because Barba replaces body
classes wholesale on every soft navigation and a mode kept there would reset
each time.

---

## Building a bespoke page

Copy `page-templates/template-ak-page.php`. Two rules it exists to demonstrate:

**Emit the Barba container.** Zeyna puts `data-barba="container"` on
`<main id="primary">` through `zeyna_barba(false)`. Call that same function
rather than hard-coding the attribute, so the theme's own transition option
still governs it. Miss it and transitions silently degrade to full browser
navigations.

**Leave `get_footer()` after `</main>`.** The footer sits outside the container
and persists across navigations — which is exactly why the Seam lives there.

Markup uses plain classes from `assets/css/ak.css`: `.ak-wrap`, `.ak-section`,
`.ak-eyebrow`, `.ak-display`, `.ak-lead`, `.ak-grid`, `.ak-btn`,
`.ak-accent-field`. No page builder, no utility framework, no build step.

**The measure frame** needs a wrapper and a HUD:

```html
<div data-ak-measure style="position:relative">
  <img src="..." alt="">
  <div class="ak-measure-hud" aria-hidden="true">
    <span class="ak-tick ak-tick--tl"></span><span class="ak-tick ak-tick--tr"></span>
    <span class="ak-tick ak-tick--bl"></span><span class="ak-tick ak-tick--br"></span>
    <span class="ak-dim ak-dim--top"></span>
    <span class="ak-dim-label">SS26 — Campaign</span>
    <span class="ak-callout ak-callout--right" style="top:35%">
      <i class="ak-callout__leader"></i>
      <b class="ak-callout__key">LOOKS</b><b class="ak-callout__value">24</b>
    </span>
  </div>
</div>
```

It opens on hover and on keyboard focus with no JavaScript at all; on touch,
`ak.js` adds `.is-measured` when the block scrolls into view. The HUD is
`aria-hidden` because it repeats figures stated elsewhere in the block.

**Hooks for the motion devices** — add these attributes to any element:

| Attribute | Effect |
|---|---|
| `data-ak-cut` | Cut-text reveal on a headline |
| `data-ak-measure` | Tech-pack annotation frame (add `data-always` to pin it open) |
| `class="ak-vf"` | Scroll-driven variable-font weight (CSS only) |
| `class="ak-draw"` | A rule that draws itself as the section scrolls through |
| `class="ak-rise"` | Short-travel arrival (CSS only) |

---

## The one design rule that is not negotiable

**The brand orange is a surface, never light-mode ink.**

Measured against this site's own surfaces:

| Pair | Contrast | Verdict |
|---|---|---|
| brand-500 on paper | 2.73:1 | **Fails** WCAG 1.4.11 — never a mark, rule or focus ring in light mode |
| brand-600 on paper | 3.49:1 | Marks in light mode |
| brand-700 on paper | 4.80:1 | Text in light mode |
| brand-500 on ink | 6.60:1 | Marks and text on dark |
| ink on brand-500 | 6.60:1 | Orange fills take **ink** type |

White on orange measures **2.94:1** and fails three thresholds at once. It is
also what nearly every orange brand ships. Do not add it back.

Use `var(--accent-fill)` for surfaces, `var(--accent-text)` for type and
`var(--accent-line)` for marks — each already resolves correctly per mode.

---

## What Zeyna already loads, and why this theme does not

Zeyna enqueues GSAP (`zeyna-gsap`), its plugin bundle (`gsap-plugins` —
SplitText, ScrollTrigger, Flip, Observer), Lenis and Barba. This child theme
declares dependencies on those handles instead of shipping duplicates: two GSAP
instances would fight over the same ticker, and it would be ~130KB of duplicate
JavaScript.

One consequence worth knowing: Zeyna's bundle does **not** include CustomEase,
so the four house easing curves are registered as plain functions via
`gsap.registerEase` in `assets/js/ak.js`. They are the same curves as the CSS
tokens, so a GSAP tween and a CSS transition on one element move identically.

---

## Before launch

- Replace `hello@akbrand.studio` in `inc/schema.php` with the real address.
- Drop the hero video into the media library, or into a `media/` folder — the
  React prototype expects `swatch.av1.mp4` + `swatch.h264.mp4`, 8–12s, silent.
- Replace the placeholder case studies and imagery.
- Supply the logotype (an SVG); the prototype sets `AK` in Fraunces italic as a
  stand-in.

---

## Reference

The React prototype is the design reference for anything not covered here —
exact spacing, motion timing, and the page compositions. See `docs/CONCEPT.md`,
`docs/MOTION.md` and `docs/WORDPRESS-ZEYNA.md` in the repository.
