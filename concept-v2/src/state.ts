/**
 * One object the scroll writes and the stage reads. Every value is 0..1
 * unless noted, and every one has a resting value that shows the letters
 * plainly — so a stalled timeline never leaves the stage in a strange place.
 */
export const state = {
  intro: 0, // letters raised on load
  split: 0, // hero → the letters at opposite edges
  stairs: 0, // edges → the lower-left of the practice frame
  wire: 0, // Strategy: the letters as a drawing
  build: 1, // Identity: how much of the solid is built
  dark: 0, // Experience: house lights down
  vox: 0, // Digital: the letters as pixels
  echo: 0, // Visibility: the letters everywhere
  exit: 0, // the letters leave the stage
  pick: 0, // live thing: -1 A steps forward, +1 K steps forward
  poseX: 0, // Image: a pose per photograph (radians)
  poseY: 0,
  poseZ: 0,
  flash: 0, // Image: flash intensity, decays in the stage
  velocity: 0, // scroll speed (px per frame, signed) from the smooth scroll
}

export type State = typeof state
