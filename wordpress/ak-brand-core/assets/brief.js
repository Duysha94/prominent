/**
 * Brief form: sends in place when JavaScript is available; without it the
 * form posts normally and the page reloads with a confirmation.
 */
(() => {
	const t = window.akBrief || {};
	document.querySelectorAll('form[data-brief]').forEach((form) => {
		const body = form.querySelector('[data-brief-body]');
		const done = form.querySelector('[data-brief-done]');
		const error = form.querySelector('[data-brief-error]');
		const button = form.querySelector('button[type="submit"]');
		const label = form.querySelector('[data-brief-label]');
		const idle = label ? label.textContent : '';

		const clear = () => {
			form.querySelectorAll('[aria-invalid]').forEach((el) => el.removeAttribute('aria-invalid'));
			form.querySelectorAll('.field__error').forEach((el) => el.remove());
			if (error) { error.hidden = true; error.textContent = ''; }
		};
		const mark = (name, message) => {
			const input = form.querySelector(`[name="${name}"]`);
			if (!input) return null;
			input.setAttribute('aria-invalid', 'true');
			const holder = input.closest('.field') || input.parentElement;
			const note = document.createElement('span');
			note.className = 'field__error';
			note.id = `${input.id || name}-error`;
			note.textContent = message;
			holder.appendChild(note);
			input.setAttribute('aria-describedby', note.id);
			return input;
		};
		const showError = (message) => {
			if (!error) return;
			error.textContent = message;
			error.hidden = false;
		};

		form.addEventListener('submit', async (e) => {
			e.preventDefault();
			clear();

			// Client-side pass first: the same rules the server applies.
			const problems = {};
			const val = (n) => (form.elements[n] ? String(form.elements[n].value || '').trim() : '');
			if (!val('ak_name')) problems.ak_name = form.elements.ak_name.validationMessage || 'Required';
			if (form.elements.ak_email && !form.elements.ak_email.checkValidity()) problems.ak_email = form.elements.ak_email.validationMessage;
			if (val('ak_message').length < 10) problems.ak_message = form.elements.ak_message.validationMessage || 'Please tell us a little more.';
			if (form.elements.ak_consent && !form.elements.ak_consent.checked) problems.ak_consent = form.elements.ak_consent.validationMessage || 'Required';
			const keys = Object.keys(problems);
			if (keys.length) {
				let first = null;
				keys.forEach((k) => { const el = mark(k, problems[k]); first = first || el; });
				showError(t.invalid || 'Please check the highlighted fields.');
				if (first) first.focus();
				return;
			}

			button.disabled = true;
			form.setAttribute('aria-busy', 'true');
			if (label) label.textContent = t.sending || 'Sending…';
			try {
				const data = new FormData(form);
				data.append('ak_ajax', '1');
				// form.action would return the hidden input named "action"; read the attribute.
				const res = await fetch(form.getAttribute('action'), { method: 'POST', body: data, credentials: 'same-origin', headers: { Accept: 'application/json' } });
				const json = await res.json().catch(() => ({ ok: false }));
				if (json.ok) {
					body.hidden = true;
					done.hidden = false;
					done.focus();
					form.dispatchEvent(new CustomEvent('ak:brief-sent', { bubbles: true }));
					return;
				}
				// A Turnstile token works once: get a fresh one for the next try.
				if (window.turnstile) form.querySelectorAll('.cf-turnstile').forEach((w) => { try { window.turnstile.reset(w); } catch (x) {} });
				let first = null;
				Object.entries(json.fields || {}).forEach(([k, m]) => { const el = mark(k, m); first = first || el; });
				showError(json.message || t.failed);
				(first || error).focus && (first || error).focus();
			} catch (err) {
				showError(t.failed || 'Something went wrong.');
			} finally {
				button.disabled = false;
				form.removeAttribute('aria-busy');
				if (label) label.textContent = idle;
			}
		});
	});
})();
