# Third-party libraries shipped with the AK theme

These are **libraries**, vendored deliberately. They are not the parent
theme's animation runtime, and the distinction is the whole point of the
Zeyna exit: needing GSAP is a library dependency, and it does not require
keeping Zeyna's 133 KB `scripts.js` and its Redux-configured implementation.

| File | Version | Source | Licence |
|---|---|---|---|
| `gsap.min.js` | 3.13.0 | npm `gsap@3.13.0`, `dist/gsap.min.js` | GreenSock Standard "no charge" licence |
| `ScrollTrigger.min.js` | 3.13.0 | npm `gsap@3.13.0`, `dist/ScrollTrigger.min.js` | as above |
| `SplitText.min.js` | 3.13.0 | npm `gsap@3.13.0`, `dist/SplitText.min.js` | as above |

Taken from GreenSock's own published npm package, **not** copied out of the
Zeyna theme. That matters: Zeyna bundles its own copies under the licence that
came with Zeyna, and carrying those forward into a theme that no longer
depends on Zeyna would be redistributing someone else's licensed files.

`SplitText` was historically a paid "Club GreenSock" plugin and is included in
the public package from 3.13.0 onward. The standard licence covers use in a
site whose visitors are not charged for the site itself, which is what this
is. **Confirm this with the studio owner before launch** — a licence question
is theirs to answer, not the build's.

Only three files are vendored, because the child theme only calls three
things: GSAP core (`quickTo`, `set`, `timeline`, `context`, `ticker`,
`registerEase`), `ScrollTrigger.refresh`, and `SplitText`. Zeyna's
`gsap-plugins.min.js` is 192 KB and carries Flip, Observer, Draggable,
MotionPath, ScrollTo, TextPlugin, EasePack and more, none of which this theme
uses.

Every use is guarded (`if (!window.gsap) return`), so a blocked or failed
library degrades to no animation rather than to a broken page.
