# AK — concept v2: “One letter each”

An immersive 3D concept for **AK Brand Development Studio**. The two letters of
the name are two people — **A is Andrii, K is Kostiantyn** — and the whole site
happens between them: A in black glass with moving pictures on its face
(digital, identity, media), K in cobalt silk (strategy and fashion production).

Read [`CONCEPT.md`](CONCEPT.md) for the idea, the argument, the art direction and
the copy deck.

```bash
npm install
npm run dev        # http://localhost:5173
npm run build      # typecheck + production build into dist/
```

`?nogl` shows the page without the 3D stage; `?still` shows it as reduced motion.

## What happens on the page

| Section | What the visitor sees |
|---|---|
| Hero | “AK” as a monument: two 3D letters laid exactly over the DOM headline. The key light follows the pointer; the shadows move with it |
| Between the letters | The letters split to opposite edges. **The live thing:** choose A or K — that letter steps forward and its founder’s half of the studio appears |
| Practice | One pinned frame, six lines that step across. The letters perform each movement: a technical drawing (Strategy), built up from the floor (Identity), a photo shoot with flashes and poses (Image), house lights down and spotlights (Experience), rebuilt as pixels (Digital), repeated into a wall of copies (Visibility) |
| Our own | London Fashion Day, Odessa Fashion Day, KEKA, Cool’baba as one line with moving pictures; the reel opens from a small frame to the whole screen |
| Studio | The description, both founders verbatim, what they bring between them |
| Begin | One word is the button: a prepared project brief by email |
| Footer | AK returns full width, each founder’s name under their letter |

On a phone the letters stand one above the other in the hero, and each
movement of the practice is its own screen.

## Structure

```
index.html            every word on the page — the 3D only ever decorates it
src/styles.css        tokens → layout → sections → phone
src/app.ts            boot: WebGL check, Lenis, stage, motion
src/state.ts          the one object scroll writes and the stage reads
src/gl/stage.ts       renderer, lights, the letters' poses and six states
src/gl/glyph.ts       extruded letters, outlines and voxels from the font
src/gl/glyphs.json    A and K outlines from Epilogue Black (npm run glyphs)
src/gl/silk.ts        tileable satin normal map
src/motion/sections.ts  every ScrollTrigger on the page
src/ui/               bar and menu, video lazy-play, the letter picker
src/film/film.ts      offline film studio for the placeholder clips (dev only)
scripts/render-clips.cjs  renders film.html frame by frame → ffmpeg → public/media
scripts/shots.cjs     screenshots of every key scroll moment for QA
```

## Video

All four clips in `public/media/` are **rendered in code** as placeholders —
no footage of the studio, its shows or its founders was available, and the
stock libraries are unreachable from the build environment. Replace them with
real London Fashion Day and Odessa Fashion Day footage (same names, 16:9,
H.264 MP4 with a poster JPG). To re-render the placeholders:

```bash
NO_HMR=1 npx vite --port 5180 &
node scripts/render-clips.cjs http://localhost:5180/
```

## Moving into WordPress

The concept is built for the classic theme rather than as a single-page app:
the markup is plain HTML with data attributes, and the 3D stage reads only the
DOM (the monument’s glyph boxes, section positions). So the port is:

1. Split `index.html` into template parts (`hero`, `letters`, `practice`, `own`,
   `studio`, `begin`, `footer`) printed by PHP with escaped content.
2. Feed them from the data the theme already models: the six movements and
   capabilities from the `ak_capability` taxonomy
   (`docs/CAPABILITIES-SYSTEM.md`), the platforms from `ak_project` with
   relationship *AK Owned*, the founders and the studio facts from options,
   the clips from the media library.
3. Enqueue the Vite build (`dist/assets/*.js` as a module, the CSS file) from
   `functions.php`; three.js is already split into its own chunk and only loads
   when WebGL2 is available and reduced motion is off.

## Budgets and fallbacks

- No WebGL2 → the letters are plain type (A in ink, K in cobalt); every section reads.
- `prefers-reduced-motion` → no smooth scroll, no pins, no flashes; the letters stand still.
- The stage stops drawing once the letters have left; videos load near the viewport and pause off-screen.
