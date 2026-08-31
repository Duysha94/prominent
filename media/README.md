# Hero video

Drop the studio's showreel loop here as two files:

- `swatch.av1.mp4` — AV1 in an mp4 container (`codecs="av01.0.05M.08"`)
- `swatch.h264.mp4` — H.264 fallback, required for Safari on older hardware

**Spec:** 8–12 seconds, silent, seamless loop, 1920×640 or wider (the swatch is
cropped to roughly 21:5 on desktop). Keep each file under ~3MB — it is
decorative and it is never the LCP element.

Until these exist the hero shows its drawn plate, which is the intended
fallback rather than a broken state. Nothing needs changing in code.
