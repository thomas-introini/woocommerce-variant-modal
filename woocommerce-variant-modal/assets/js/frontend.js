(function ($) {
	'use strict';

	const $body = $(document.body);
	let lastFocused = null;
	let adding = false;

	function lockScroll(lock) {
		if (WCVM.behavior.lockScroll) {
			document.documentElement.style.overflow = lock ? 'hidden' : '';
		}
	}

	function openModal(productId) {
		const $overlay = $('#wcvm-overlay');
		const $dialog = $('#wcvm-dialog');
		const $content = $('#wcvm-content');

		$overlay.removeAttr('hidden');
		$dialog.removeAttr('hidden');

		lockScroll(true);

		// Fetch modal content
		$content.html('<div class="wcvm-loading" role="status" aria-live="polite">…</div>');
		$.post(WCVM.ajaxUrl, { action: 'wcvm_get_modal', nonce: WCVM.nonce, product_id: productId })
			.done(function (res) {
				if (!res || !res.success) { throw new Error('Request failed'); }
				$('#wcvm-title').text(res.data.title);
				// $('#wcvm-title').text($(res.data.title).text());
				$content.html(res.data.html);

				// Initialize Woo variation form logic.
				const $form = $content.find('form.variations_form');

				$form.wc_variation_form();
				$form.trigger('check_variations');
				$form.find('input[name="add-to-cart"]').val($form.data('product_id'));

				$form.find('button.single_add_to_cart_button').on('click', function (e) {
					e.preventDefault();
					e.stopImmediatePropagation();
					$form.trigger('submit');
				});

				// Show/hide reset link automatically.
				$form.on('woocommerce_variation_has_changed found_variation', function () {
					$form.find('.reset_variations').toggle($form.find('select').filter(function () { return this.value; }).length > 0);
				});

				// Update stock area, price lives inside .single_variation.
				$form.on('found_variation', function (e, variation) {
					if (WCVM.showStock) {
						const $stock = $form.closest('#wcvm-content').find('.wcvm-stock');
						if (variation && variation.availability_html) {
							$stock.html(variation.availability_html);
						} else {
							$stock.empty();
						}
					}
				});

				// Intercept submit -> AJAX add to cart (so we stay on the page).
				$form.on('submit', function (e) {
					e.preventDefault();
					e.stopImmediatePropagation();
					if (adding) return;
					adding = true;
					const $btn = $form.find('button.single_add_to_cart_button');
					if ($btn.prop('disabled')) return;

					// Require a chosen variation
					const variationId = parseInt($form.find('input.variation_id').val() || '0', 10);
					if (!variationId) {
						return;
					}

					$btn.prop('disabled', true).addClass('loading');

					const payload = $form.serialize();
					const url = WCVM.wcAjaxUrl.replace('%%endpoint%%', 'add_to_cart');

					$.post(url, payload)
						.done(function (response) {
							// Woo format: { fragments: {...}, cart_hash: '' } or error messages
							if (response && response.fragments) {
								$.each(response.fragments, function (key, value) {
                                    // Replace fragments (minicart, notices, etc.)
									$(key).replaceWith(value);
								});
								$body.trigger('wc_fragment_refresh');
								$body.trigger('added_to_cart', [response.fragments, response.cart_hash, $btn]);
								closeModal();
							} else if (response && response.error && response.product_url) {
								// Fallback: redirect to product page if something went wrong (e.g., required fields)
								window.location = response.product_url;
							}
						})
						.fail(function () {
							window.alert('Errore nell\'aggiunta al carrello.');
						})
						.always(function () {
							$btn.prop('disabled', false).removeClass('loading');
						});
				});

				// Focus trap: focus content region
				setTimeout(() => {
					const first = $dialog.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])').filter(':visible').first();
					if (first.length) first.trigger('focus');
				}, 10);
			})
			.fail(function () {
				$content.html('<p class="wcvm-error" role="alert">Impossibile caricare le opzioni.</p>');
			});
	}

	function closeModal() {
		$('#wcvm-overlay').attr('hidden', true);
		$('#wcvm-dialog').attr('hidden', true).find('#wcvm-content').empty();
		lockScroll(false);
		if (lastFocused) {
			lastFocused.focus();
			lastFocused = null;
		}
	}

	// Open handler: click on archive variable product "choose" button
	$(document).on('click', 'a.wcvm-open-modal[data-product_id]', function (e) {
		// Non-single pages only: if already on single, let default.
		if ($('body').hasClass('single-product')) return;

		e.preventDefault();
		lastFocused = this;
		openModal(parseInt($(this).data('product_id'), 10));
	});

	// Close handlers
	$(document).on('click', '.wcvm-close', function () {
		closeModal();
	});
	$('#wcvm-overlay').on('click', function () {
		console.log('click');
		if (WCVM.behavior.closeOnBackdrop) closeModal();
	});
	$(document).on('keydown', function (e) {
		if (e.key === 'Escape' && WCVM.behavior.closeOnEsc && !$('#wcvm-dialog').is('[hidden]')) {
			closeModal();
		}
	});
})(jQuery);
