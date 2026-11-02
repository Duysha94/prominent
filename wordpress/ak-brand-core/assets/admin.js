/* Project details: pick gallery images, a film or a loop from the media library. */
(function ($) {
	$(document).on('click', '[data-ak-media]', function (e) {
		e.preventDefault();
		var btn = $(this);
		var input = $('#' + btn.data('ak-media'));
		var multiple = String(btn.data('multiple')) === '1';
		var frame = wp.media({
			title: btn.text(),
			multiple: multiple ? 'add' : false,
			library: { type: btn.data('type') || 'image' }
		});
		frame.on('open', function () {
			var sel = frame.state().get('selection');
			String(input.val() || '').split(',').forEach(function (id) {
				id = parseInt(id, 10);
				if (id) { var a = wp.media.attachment(id); a.fetch(); sel.add(a); }
			});
		});
		frame.on('select', function () {
			var ids = frame.state().get('selection').map(function (a) { return a.id; });
			input.val(multiple ? ids.join(',') : (ids[0] || ''));
		});
		frame.open();
	});
})(jQuery);
