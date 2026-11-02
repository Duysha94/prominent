// Extracts the outlines of the letters the 3D stage needs from Epilogue Black,
// in em units with y pointing up, so the browser can build THREE.Shapes from
// them without shipping a font parser.
import { readFileSync, writeFileSync } from 'node:fs'
import { fileURLToPath } from 'node:url'
import opentype from 'opentype.js'

const root = fileURLToPath(new URL('..', import.meta.url))
const fontFile = `${root}node_modules/@fontsource/epilogue/files/epilogue-latin-900-normal.woff`
const buf = readFileSync(fontFile)
const font = opentype.parse(buf.buffer.slice(buf.byteOffset, buf.byteOffset + buf.byteLength))
const em = font.unitsPerEm

const out = {
  font: 'Epilogue 900',
  unitsPerEm: em,
  ascender: font.ascender / em,
  descender: font.descender / em,
  capHeight: (font.tables.os2?.sCapHeight ?? 0) / em,
  glyphs: {},
}

const r = (n) => Math.round(n * 1e5) / 1e5

for (const ch of ['A', 'K']) {
  const glyph = font.charToGlyph(ch)
  const cmds = []
  // getPath() is y-down; flip back to the font's y-up space, scaled to 1 em.
  for (const c of glyph.getPath(0, 0, 1).commands) {
    if (c.type === 'M' || c.type === 'L') cmds.push([c.type, r(c.x), r(-c.y)])
    else if (c.type === 'Q') cmds.push(['Q', r(c.x1), r(-c.y1), r(c.x), r(-c.y)])
    else if (c.type === 'C') cmds.push(['C', r(c.x1), r(-c.y1), r(c.x2), r(-c.y2), r(c.x), r(-c.y)])
    else if (c.type === 'Z') cmds.push(['Z'])
  }
  const bb = glyph.getBoundingBox()
  out.glyphs[ch] = {
    advance: r(glyph.advanceWidth / em),
    bbox: [r(bb.x1 / em), r(bb.y1 / em), r(bb.x2 / em), r(bb.y2 / em)],
    commands: cmds,
  }
}

const kern = font.getKerningValue(font.charToGlyph('A'), font.charToGlyph('K'))
out.kerningAK = r(kern / em)

writeFileSync(`${root}src/gl/glyphs.json`, JSON.stringify(out))
console.log('glyphs.json:', Object.keys(out.glyphs).join(' '), 'capHeight', out.capHeight, 'kernAK', out.kerningAK,
  Object.entries(out.glyphs).map(([k, g]) => `${k}: ${g.commands.length} cmds bbox ${g.bbox}`).join(' | '))
