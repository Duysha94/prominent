/**
 * The project editor's conditional panels.
 *
 * The Project Type radio is the control. Choosing it reveals that type's
 * panels and hides the rest; choosing nothing leaves the always-visible panel
 * and the website module, which together are a complete publishable record.
 *
 * No build step, no framework, no ACF.
 */
(function () {
	'use strict';

	var cfg = window.akProjectAdmin || {};
	var ALWAYS = ['always', 'website'];

	function boxFor(panel) {
		return document.getElementById('ak_panel_' + panel);
	}

	function modeFor(termId) {
		var slug = cfg.terms && cfg.terms[termId];
		return (slug && cfg.modes && cfg.modes[slug]) || 'record';
	}

	function apply() {
		var checked = document.querySelector('input[name="ak_tax_ak_project_type"]:checked');
		var mode = checked ? modeFor(checked.value) : 'record';
		var visible = ALWAYS.concat((cfg.panels && cfg.panels[mode]) || []);

		Object.keys(cfg.panels || {}).forEach(function (m) {
			(cfg.panels[m] || []).forEach(function (panel) {
				var box = boxFor(panel);
				if (box) { box.hidden = visible.indexOf(panel) === -1; }
			});
		});
	}

	document.addEventListener('change', function (e) {
		if (e.target.name === 'ak_tax_ak_project_type') { apply(); }
	});

	/* The media picker. The number input stays authoritative so the field
	   still works with JS unavailable — the picker only fills it in. */
	document.addEventListener('click', function (e) {
		var button = e.target.closest('.ak-pick-media');
		if (!button || !window.wp || !window.wp.media) { return; }
		e.preventDefault();
		var input = document.getElementById(button.dataset.target);
		var frame = window.wp.media({ title: button.textContent, multiple: false });
		frame.on('select', function () {
			var item = frame.state().get('selection').first().toJSON();
			input.value = item.id;
			var name = button.parentNode.querySelector('.ak-media-name');
			if (name) { name.textContent = item.title || ''; }
		});
		frame.open();
	});

	apply();
})();
