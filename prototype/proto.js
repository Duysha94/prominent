/* AK prototype — interaction layer.
   The only script in the prototype. It exists because the proof-of-concept has
   to be interaction-complete on its own: the shipped child theme has a working
   panel, but a prototype that borrows credit for behaviour it does not have
   cannot be reviewed honestly on a phone. */
(function () {
  'use strict';

  var root = document.documentElement;
  var toggle = document.querySelector('[data-nav-toggle]');
  var panel = document.getElementById('navp');
  if (!toggle || !panel) { return; }

  var FOCUSABLE = 'a[href],button:not([disabled]),input,select,textarea,[tabindex]:not([tabindex="-1"])';
  var lastFocused = null;

  function focusable() {
    return Array.prototype.filter.call(
      panel.querySelectorAll(FOCUSABLE),
      function (el) { return el.offsetParent !== null; }
    );
  }

  function open() {
    lastFocused = document.activeElement;
    panel.hidden = false;
    /* Two frames: hidden -> laid out -> transitioned. One frame and the
       browser coalesces both style changes, so the panel appears instantly
       with no motion at all. */
    requestAnimationFrame(function () {
      requestAnimationFrame(function () { root.classList.add('nav-open'); });
    });
    toggle.setAttribute('aria-expanded', 'true');
    var first = focusable()[0];
    if (first) { first.focus(); }
    document.addEventListener('keydown', onKey, true);
  }

  function close() {
    root.classList.remove('nav-open');
    toggle.setAttribute('aria-expanded', 'false');
    document.removeEventListener('keydown', onKey, true);
    var done = function () { panel.hidden = true; };
    /* Hiding on transitionend alone strands the panel open when the user
       prefers reduced motion and no transition ever fires. */
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      done();
    } else {
      window.setTimeout(done, 320);
    }
    if (lastFocused && document.contains(lastFocused)) { lastFocused.focus(); }
  }

  function onKey(e) {
    if (e.key === 'Escape') { e.preventDefault(); close(); return; }
    if (e.key !== 'Tab') { return; }
    var items = focusable();
    if (!items.length) { return; }
    var first = items[0];
    var last = items[items.length - 1];
    if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
    else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
  }

  toggle.addEventListener('click', function () {
    if (root.classList.contains('nav-open')) { close(); } else { open(); }
  });

  panel.addEventListener('click', function (e) {
    if (e.target.closest('[data-nav-close]')) { close(); }
  });

  /* The panel is a phone control. Resizing past the breakpoint with it open
     would otherwise leave the scroll lock and the scrim in place over a
     desktop layout. */
  window.matchMedia('(min-width: 901px)').addEventListener('change', function (e) {
    if (e.matches && root.classList.contains('nav-open')) { close(); }
  });
})();
