Translation files live here.

The theme registers this folder on `after_setup_theme`, so a `.mo` file
named for its locale — `ak-zeyna-child-fr_FR.mo`, `ak-zeyna-child-de_DE.mo` —
is picked up with no code change. Generate the `.pot` template with WP-CLI:

    wp i18n make-pot . languages/ak-zeyna-child.pot

The site ships English only; nothing here is required for it to run.
