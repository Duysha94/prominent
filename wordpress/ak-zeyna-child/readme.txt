=== AK Brand Development Studio ===

Contributors: akstudio
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 1.2.0
Template: zeyna
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Child theme for Zeyna carrying the AK design system, its own chrome and its
own content.

== Description ==

A child theme for the commercial Zeyna theme, built for AK Brand Development
Studio (Fashion & Brand Advisory, London).

It supplies:

* the studio's colour, type and motion system, in two designed modes
  (ATELIER / RUNWAY) that a visitor can switch at any time;
* its own header, footer and page loader, so a Zeyna demo import cannot
  replace them;
* every page of the site as template files, with the content shipped inside
  the theme and reconciled automatically after each update;
* structured data, per-page meta descriptions and internal linking;
* a Contact Form 7 project brief, created on activation.

== Requirements ==

* The Zeyna parent theme (installed; it does not need to be active).
* Contact Form 7, for the project brief. Without it the contact page falls
  back to a mailto link and nothing breaks.

== Installation ==

1. Install the Zeyna parent theme.
2. Install and activate Contact Form 7.
3. Upload this theme and activate it. It creates its pages, the navigation
   menu and the contact form by itself; no content import is required.
4. Add the two logotypes and the showreel under Appearance → Customize →
   AK Studio.

== Content synchronisation ==

After every theme update the theme reconciles the site with the content it
ships: missing pages are created, untouched pages are refreshed, pages the
theme no longer ships are moved to the Trash, and anything edited by hand is
left exactly as it is. What it did is reported in the admin as a notice.

== Changelog ==

= 1.2.0 =
* Own header, footer and page loader; the boot sequence no longer depends on
  the parent theme's Redux configuration.
* Two logotypes, one per mode, swapped in the same frame as the mode.
* Mode switch moved into the header bar so it is reachable on a phone.
* Content synchronisation on update, with a report and edit protection.
* The navigation menu and the contact form are created by the theme.
* Type scale rebased: the parent's 14px root was compressing every size.
* Fixed horizontal overflow at every width between 600 and 910 pixels.
* Tap targets raised to the WCAG 2.2 minimum.
* Decorative section numbering and the counter tiles removed.

= 1.1.0 =
* Forced the theme's own header and footer over Zeyna demo templates.
* Full-screen showreel; straight seam; self-update channel.

= 1.0.0 =
* First release.
