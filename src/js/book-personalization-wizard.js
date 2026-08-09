(function ($) {
	'use strict';

	var config = window.tlthBookWizard || {};
	var i18n = config.i18n || {};

	function wizardLog() {
		var args = Array.prototype.slice.call(arguments);
		args.unshift('[tlthBookWizard]');
		console.warn.apply(console, args);
	}

	function wizardDebug() {
		if (!config.debug) {
			return;
		}
		var args = Array.prototype.slice.call(arguments);
		args.unshift('[tlthBookWizard]');
		console.log.apply(console, args);
	}

	// WC GF add-on calls gf_apply_rules on found_variation; product embeds may lack logic data.
	function patchGfApplyRulesSafe() {
		if (window.gf_apply_rules && window.gf_apply_rules._tlthPatched) {
			return;
		}

		var native = typeof window.gf_apply_rules === 'function' ? window.gf_apply_rules : null;

		window.gf_apply_rules = function (formId, fields) {
			var logic = window.gf_form_conditional_logic;
			if (!logic || !logic[formId]) {
				wizardDebug('gf_apply_rules skipped — no conditional logic for form', formId);
				return;
			}
			if (!native) {
				return;
			}
			try {
				return native.call(this, formId, fields);
			} catch (err) {
				wizardDebug('gf_apply_rules error', err);
			}
		};
		window.gf_apply_rules._tlthPatched = true;
	}

	patchGfApplyRulesSafe();

	function initBookPersonalizationWizard() {
		var $root = $('[data-book-wizard]');
		var $form = $root.find('form.cart, form.variations_form').first();

		if (!$form.length || $form.data('wizardInitialized')) {
			return;
		}

		var $chrome = $form.find('[data-book-wizard-chrome]');
		var $progress = $chrome.find('[data-book-wizard-progress]');
		var $rows = $form.find('table.variations tbody tr');
		var $variationWrap = $form.find('.single_variation_wrap');
		var variationCount = $rows.length;
		var gfPageCount = parseInt(config.gfPageCount, 10) || 1;
		var totalSteps = variationCount + gfPageCount;
		var currentStep = 1;
		var optionImage = config.optionImage || '';
		var gfFormId = getGfFormId($form) || config.formId;

		if (variationCount < 1) {
			return;
		}

		$form.data('wizardInitialized', true);
		$form.data('tlthVariationCount', variationCount);
		$root.addClass('is-wizard-active');
		$form.addClass('book-wizard-form');

		var $gfWrapper = $form.find('.gform_wrapper, .gform_variation_wrapper').first();

		$chrome.removeAttr('hidden');

		orderVariationRows($rows);
		$rows = $form.find('table.variations tbody tr');

		$rows.each(function (index) {
			var $row = $(this);
			var attrName = getRowAttributeName($row);
			var stepNum = index + 1;
			var title = (config.stepTitles && config.stepTitles[attrName]) || $row.find('td.label label').text().trim();

			$row.addClass('book-wizard__step book-wizard__step--variation')
				.attr('data-wizard-step', stepNum)
				.attr('data-step-title', title);

			buildVariationCards($row, optionImage, $form);
		});

		refreshVariationCardsStock($form);

		buildProgress($progress, totalSteps);

		// Move any legacy footer out of the GF wrap (prevents duplicates after AJAX).
		var $legacyFooter = $form.find('[data-book-wizard-footer]').first();
		if ($legacyFooter.length && !$root.find('[data-book-wizard-footer]').length) {
			$root.append($legacyFooter);
		}

		buildFooter($root, $form, $variationWrap);
		ensureFooterAddToCartButton($root);
		buildSuccessPanel($root);
		bindAddToCartHandler($form, $root, gfFormId);

		var $stepHeading = $form.find('[data-book-wizard-heading]');

		if (!$stepHeading.length) {
			$stepHeading = $('<h3 class="book-wizard__heading text-xl md:text-2xl font-medium text-deep-blue text-center mb-6" data-book-wizard-heading></h3>');
			$chrome.after($stepHeading);
		}

		hideGfProgress($form.find('.gform_wrapper').first());
		$gfWrapper.addClass('book-wizard__gform');

		overrideWcGfPagingHandlers($form);
		bindGfAjaxSubmit($form, function (targetPage) {
			onGfAjaxPageChange(targetPage);
		});
		bindNativeGfButtonIntercept($root, $form, gfFormId);

		setTimeout(function () {
			overrideWcGfPagingHandlers($form);
		}, 0);
		setTimeout(function () {
			overrideWcGfPagingHandlers($form);
		}, 500);

		function onGfAjaxPageChange(targetPage) {
			$gfWrapper = $form.find('.gform_wrapper, .gform_variation_wrapper').first();
			hideGfProgress($gfWrapper);
			overrideWcGfPagingHandlers($form);
			dedupeWizardFooter($root);
			ensureFooterAddToCartButton($root);

			if (!$root.hasClass('is-added-to-cart')) {
				$form.find('.gform_confirmation_wrapper, .gform_confirmation_message').remove();
			}

			var gfPage = targetPage || getGfCurrentPage($form, gfFormId);
			goToStep(variationCount + gfPage);
		}

		function goToStep(step) {
			currentStep = step;
			var onGf = step > variationCount;
			var isLast = step === totalSteps;

			$form.attr('data-current-step', step);

			if (onGf) {
				var gfPage = step - variationCount;
				$form.data('tlthWizardGfPage', gfPage);
				syncGfPagingFields($form, gfFormId, gfPage, '0');
			}
			$root.toggleClass('is-gf-phase', onGf);
			$root.toggleClass('is-last-step', isLast);
			$form.toggleClass('is-gf-phase', onGf);
			$form.toggleClass('is-last-step', isLast);

			$rows.removeClass('is-active');
			if (step <= variationCount) {
				$rows.filter('[data-wizard-step="' + step + '"]').addClass('is-active');
			}

			if (step <= variationCount) {
				var $activeRow = $rows.filter('[data-wizard-step="' + step + '"]');
				$stepHeading.text($activeRow.attr('data-step-title') || '');
			} else {
				$stepHeading.text('');
			}

			updateProgress($progress, step, totalSteps);

			$root.find('[data-book-wizard-prev]').prop('disabled', step === 1);
			$root.find('[data-book-wizard-next]').toggleClass('hidden', isLast);
			$root.find('[data-book-wizard-add-to-cart]').toggleClass('hidden', !isLast);

			setupLastGfPage($form, $root, gfFormId, isLast);
			dedupeWizardFooter($root);
			syncFooterPrice($root, $form, $variationWrap);
		}

		$root.addClass('is-wizard-ready');
		$form.addClass('is-wizard-ready');
		goToStep(1);

		$root.on('click.tlthWizardNav', '[data-book-wizard-next]', function (e) {
			e.preventDefault();
			if (currentStep <= variationCount) {
				if (!validateVariationStep(currentStep)) {
					showVariationError();
					return;
				}
				if (currentStep === variationCount) {
					syncVariationId($form).done(function () {
						goToStep(currentStep + 1);
					});
					return;
				}
				goToStep(currentStep + 1);
				return;
			}

			navigateGfPage($form, 'next', gfFormId);
		});

		$root.on('click.tlthWizardNav', '[data-book-wizard-prev]', function (e) {
			e.preventDefault();
			if (currentStep <= variationCount) {
				if (currentStep > 1) {
					goToStep(currentStep - 1);
				}
				return;
			}

			if (currentStep === variationCount + 1) {
				goToStep(variationCount);
				return;
			}

			navigateGfPage($form, 'prev', gfFormId);
		});

		$form.on('change', 'input[type="radio"][name^="attribute_"]', function () {
			if ($(this).prop('disabled')) {
				return;
			}
			$form.find('.book-wizard-variation-card').removeClass('is-selected');
			$(this).closest('.book-wizard-variation-card').addClass('is-selected');
			$form.find('.book-wizard__error').remove();
			refreshVariationCardsStock($form);
			$form.trigger('check_variations');
			syncFooterPrice($root, $form, $variationWrap);
		});

		$form.on('found_variation', function (event, variation) {
			if (variation && variation.variation_id) {
				$form.data('tlthVariationId', variation.variation_id);
				$form.find('input[name="variation_id"]').val(variation.variation_id);
			}
			syncFooterPrice($root, $form, $variationWrap);
		});

		$form.on('reset_data', function () {
			$form.removeData('tlthVariationId');
			syncFooterPrice($root, $form, $variationWrap);
		});

		$form.on('tlthGfPageChanged', function (event, page) {
			onGfAjaxPageChange(page);
		});

		$(document).on('gform_page_loaded.tlthBookWizard', function (event, formId, currentPage) {
			if (parseInt(formId, 10) !== parseInt(gfFormId, 10)) {
				return;
			}
			if (!$form.find('.gform_wrapper').length) {
				return;
			}
			wizardLog('gform_page_loaded', { formId: formId, currentPage: currentPage });
			onGfAjaxPageChange(currentPage);
		});

		function validateVariationStep(step) {
			var $row = $rows.filter('[data-wizard-step="' + step + '"]');
			return (
				$row.find('input[type="radio"]:checked:not(:disabled), select').filter(function () {
					return $(this).val();
				}).length > 0 &&
				$row.find('.book-wizard-variation-card.is-out-of-stock input:checked').length === 0
			);
		}

		function showVariationError() {
			$form.find('.book-wizard__error').remove();
			$stepHeading.after(
				'<p class="book-wizard__error text-center text-red text-sm mb-4">' +
					(i18n.selectOne || 'Choisis une option pour continuer.') +
					'</p>'
			);
		}
	}

	function orderVariationRows($rows) {
		var order = config.attributeOrder || [];
		if (!order.length) {
			return;
		}

		var $tbody = $rows.first().parent();
		order.forEach(function (attr) {
			var $match = $rows.filter(function () {
				return getRowAttributeName($(this)) === attr;
			});
			if ($match.length) {
				$tbody.append($match);
			}
		});
	}

	function getRowAttributeName($row) {
		var $select = $row.find('select[name^="attribute_"]');
		if ($select.length) {
			return $select.attr('name').replace('attribute_', '');
		}
		var $radio = $row.find('input[type="radio"][name^="attribute_"]').first();
		if ($radio.length) {
			return $radio.attr('name').replace('attribute_', '');
		}
		return '';
	}

	function getProductVariations($form) {
		var variations = $form.data('product_variations');

		if (typeof variations === 'string') {
			try {
				variations = JSON.parse(variations);
			} catch (err) {
				variations = [];
			}
		}

		return variations && variations.length ? variations : [];
	}

	function attributeKeyFromRowName(attrName) {
		if (!attrName) {
			return '';
		}

		return attrName.indexOf('attribute_') === 0 ? attrName : 'attribute_' + attrName;
	}

	function isVariationInStock(variation) {
		if (!variation) {
			return false;
		}
		if (variation.variation_is_active === false || variation.variation_is_visible === false) {
			return false;
		}
		if (variation.is_in_stock === false || variation.is_purchasable === false) {
			return false;
		}

		return true;
	}

	function variationMatchesAttributes(variation, testAttrs) {
		var keys = Object.keys(testAttrs);

		for (var k = 0; k < keys.length; k++) {
			var key = keys[k];
			var varVal = variation.attributes[key];

			if (varVal !== undefined && varVal !== '' && varVal !== testAttrs[key]) {
				return false;
			}
		}

		return true;
	}

	function getVariationAttributeKeyForRow($row) {
		var $input = $row.find('input[type="radio"][name^="attribute_"]').first();
		if ($input.length) {
			return normalizeAttributeFieldName($input.attr('name'));
		}

		var $select = $row.find('select[name^="attribute_"]');
		if ($select.length) {
			return normalizeAttributeFieldName($select.attr('name'));
		}

		return attributeKeyFromRowName(getRowAttributeName($row));
	}

	function getPartialAttributesBeforeRow($form, $row) {
		var partial = {};

		$form.find('tr.book-wizard__step--variation').each(function () {
			var $r = $(this);
			if ($r[0] === $row[0]) {
				return false;
			}

			var key = getVariationAttributeKeyForRow($r);
			var val = $r.find('input[type="radio"]:checked').val() || $r.find('select').val();

			if (key && val) {
				partial[key] = val;
			}
		});

		return partial;
	}

	function isOptionValueInStock($form, $row, optionValue) {
		if (!optionValue) {
			return false;
		}

		var variations = getProductVariations($form);
		if (!variations.length) {
			return true;
		}

		var attrKey = getVariationAttributeKeyForRow($row);
		if (!attrKey) {
			return true;
		}

		var testAttrs = getPartialAttributesBeforeRow($form, $row);
		testAttrs[attrKey] = optionValue;

		for (var i = 0; i < variations.length; i++) {
			var variation = variations[i];
			if (!isVariationInStock(variation)) {
				continue;
			}
			if (variationMatchesAttributes(variation, testAttrs)) {
				return true;
			}
		}

		return false;
	}

	function applyVariationCardStockState($form, $row, $card) {
		var $input = $card.find('input[type="radio"]');
		var optionValue = $input.val();
		var inStock = isOptionValueInStock($form, $row, optionValue);
		var overlayText = i18n.outOfStock || 'Rupture de stock';

		$card.toggleClass('is-out-of-stock', !inStock);
		$input.prop('disabled', !inStock);

		$card.find('.book-wizard-variation-card__overlay').remove();

		if (!inStock) {
			$card.append(
				'<span class="book-wizard-variation-card__overlay" aria-hidden="true">' +
					'<span class="book-wizard-variation-card__overlay-text">' +
					overlayText +
					'</span></span>'
			);
			if ($input.prop('checked')) {
				$input.prop('checked', false);
				$card.removeClass('is-selected');
			}
		}
	}

	function refreshVariationCardsStock($form) {
		$form.find('tr.book-wizard__step--variation').each(function () {
			var $row = $(this);
			$row.find('.book-wizard-variation-card').each(function () {
				applyVariationCardStockState($form, $row, $(this));
			});
		});
	}

	function buildVariationCards($row, imageUrl, $form) {
		var $value = $row.find('td.value');
		var $options = $row.find('ul li');

		if (!$options.length) {
			var $radios = $value.find('input[type="radio"]');
			if ($radios.length) {
				$options = $radios.map(function () {
					var $input = $(this);
					var $wrap = $input.closest('label, li, span, div').first();
					return ($wrap.length ? $wrap[0] : this);
				});
			}
		}

		if (!$options.length) {
			return;
		}

		var $grid = $('<div class="book-wizard-variation-grid" />');

		$options.each(function () {
			var $li = $(this);
			var $input = $li.find('input[type="radio"]');
			var labelText = $li.find('label').first().text().trim();
			var isChecked = $input.prop('checked');

			var $card = $('<label class="book-wizard-variation-card' + (isChecked ? ' is-selected' : '') + '"></label>');

			if (imageUrl) {
				$card.append('<span class="book-wizard-variation-card__img"><img src="' + imageUrl + '" alt="" loading="lazy" /></span>');
			}

			$card.append('<span class="book-wizard-variation-card__radio" aria-hidden="true"></span>');
			$card.append('<span class="book-wizard-variation-card__label">' + labelText + '</span>');
			$input.appendTo($card);

			if ($form) {
				applyVariationCardStockState($form, $row, $card);
			}

			$grid.append($card);
			$li.remove();
		});

		$value.find('ul').remove();
		$value.prepend($grid);
	}

	function buildProgress($progress, total) {
		$progress.empty();
		$progress.append(
			'<span class="book-wizard-progress__compact" data-book-wizard-progress-compact aria-live="polite">' +
				'<span class="book-wizard-progress__compact-dot"></span>' +
				'</span>' +
				'<div class="book-wizard-progress__track"></div>'
		);

		var $track = $progress.find('.book-wizard-progress__track');
		for (var i = 1; i <= total; i++) {
			$track.append(
				'<span class="book-wizard-progress__item" data-step="' +
					i +
					'"><span class="book-wizard-progress__dot"></span></span>'
			);
			if (i < total) {
				$track.append('<span class="book-wizard-progress__line" aria-hidden="true"></span>');
			}
		}
	}

	function dedupeWizardFooter($root) {
		$root.find('[data-book-wizard-footer]').slice(1).remove();
	}

	function ensureFooterAddToCartButton($root) {
		if ($root.find('[data-book-wizard-add-to-cart]').length) {
			return;
		}

		var $navActions = $root.find('.book-wizard-footer__nav-actions').first();
		var $nav = $navActions.length ? $navActions : $root.find('.book-wizard-footer__nav').first();
		if (!$nav.length) {
			return;
		}

		$nav.append(
			'<button type="button" class="book-wizard-nav book-wizard-nav--add-to-cart btn hidden" data-book-wizard-add-to-cart>' +
				(i18n.addToCart || 'Ajouter au panier') +
				'</button>'
		);
	}

	function buildSuccessPanel($root) {
		if ($root.find('[data-book-wizard-success]').length) {
			return;
		}

		var cartUrl = config.cartUrl || '/panier/';
		var btnLabel = i18n.viewCart || 'Voir le panier';

		$root.append(
			'<div class="book-wizard-success text-center py-10 px-4" data-book-wizard-success hidden>' +
				'<h3 class="book-wizard-success__title text-xl md:text-2xl font-medium text-deep-blue mb-6"></h3>' +
				'<a href="' +
				cartUrl +
				'" class="book-wizard-success__cta btn inline-flex items-center justify-center px-8 py-2.5 rounded-full bg-deep-blue text-white border-2 border-deep-blue text-[15px] font-semibold hover:bg-teal hover:border-teal transition-colors">' +
				btnLabel +
				'</a>' +
				'</div>'
		);
	}

	function getAddToCartQuantity($form) {
		var qty = parseInt($form.find('input.qty, input[name="quantity"]').val(), 10);
		return qty > 0 ? qty : 1;
	}

	function showCartConfirmation($root, $form) {
		var qty = $form && $form.length ? getAddToCartQuantity($form) : 1;
		var title =
			qty > 1
				? i18n.addedTitlePlural || 'Produits ajoutés au panier'
				: i18n.addedTitle || 'Produit ajouté au panier';

		$root.find('.book-wizard-success__title').text(title);
		$root.addClass('is-added-to-cart');
		$root.find('[data-book-wizard-success]').removeAttr('hidden');
		scrollToWizard();
	}

	function isWooCommerceCartNoticeHtml(html) {
		if (!html || typeof html !== 'string') {
			return false;
		}

		var lower = html.toLowerCase();

		if (lower.indexOf('gform_confirmation_wrapper') !== -1 && lower.indexOf('woocommerce-message') === -1) {
			return false;
		}

		if (lower.indexOf('woocommerce-message') === -1) {
			return false;
		}

		var cartPhrases = [
			'ajouté au panier',
			'ajouté à votre panier',
			'a été ajouté',
			'ont été ajoutés',
			'added to your cart',
			'has been added to your cart',
		];

		return cartPhrases.some(function (phrase) {
			return lower.indexOf(phrase) !== -1;
		});
	}

	function fetchCartSnapshot(productId, variationId) {
		return $.ajax({
			url: config.ajaxUrl || window.ajaxurl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'tlth_book_wizard_cart_snapshot',
				nonce: config.nonce || '',
				product_id: productId,
				variation_id: variationId || 0,
			},
		}).then(
			function (res) {
				return res;
			},
			function () {
				return { success: false, data: { count: 0, has_product: false } };
			}
		);
	}

	function responseHasGfValidation(html, formId) {
		if (!html || typeof html !== 'string') {
			return false;
		}

		var trimmed = html.trim();
		if (trimmed.charAt(0) === '{' || trimmed.charAt(0) === '[') {
			return (
				trimmed.indexOf('"status":"validation_failed"') !== -1 ||
				trimmed.indexOf('"status": "validation_failed"') !== -1
			);
		}

		formId = parseInt(formId, 10) || parseInt(config.formId, 10) || 0;
		var $doc = parseGfResponseDocument(html);
		var $wrap = formId ? $doc.find('#gform_wrapper_' + formId).first() : $();
		if (!$wrap.length) {
			$wrap = $doc.find('.gform_wrapper').first();
		}
		if (!$wrap.length) {
			return false;
		}

		return (
			$wrap.hasClass('gform_validation_error') ||
			$wrap.find('.gform_validation_errors, .gfield_error').length > 0
		);
	}

	function responseHasWooCommerceError(html) {
		if (!html || typeof html !== 'string') {
			return false;
		}

		var $doc = parseGfResponseDocument(html);
		return $doc.find('.woocommerce-notices-wrapper .woocommerce-error, ul.woocommerce-error').length > 0;
	}

	function formHasGfValidationErrors($form, formId) {
		var $wrap = getGfWrapper($form, formId);

		if (!$wrap.length) {
			$wrap = $form.find('.gform_wrapper').first();
		}

		return (
			$wrap.hasClass('gform_validation_error') ||
			$wrap.find('.gform_validation_errors').length > 0 ||
			$form.find('.gfield_error:visible, .gfield_validation_message:visible').length > 0
		);
	}

	function focusGfValidationErrors($form, formId) {
		var lastPage = getLastGfPage($form, formId);
		showGfPageInWizard($form, formId, lastPage);

		var $firstError = $form.find('.gfield_error, .gfield_validation_message').filter(':visible').first();
		if ($firstError.length) {
			$firstError[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
		}
	}

	function validateGfFormForSubmit($form, formId) {
		formId = parseInt(formId, 10) || getGfFormId($form);
		if (!formId || !$form || !$form.length) {
			return false;
		}

		prepareGfFormForAddToCart($form, formId);

		var valid = null;

		if (window.gform && window.gform.utils) {
			if (typeof window.gform.utils.validateForm === 'function') {
				valid = window.gform.utils.validateForm(formId);
			} else if (typeof window.gform.utils.validateCurrentPage === 'function') {
				valid = window.gform.utils.validateCurrentPage(formId);
			}
		} else if (window.gform && typeof window.gform.validateForm === 'function') {
			valid = window.gform.validateForm(formId);
		} else if (typeof window['gf_validate_' + formId] === 'function') {
			valid = window['gf_validate_' + formId]();
		}

		if (valid === null) {
			valid = false;
		}

		if (!valid || formHasGfValidationErrors($form, formId)) {
			focusGfValidationErrors($form, formId);
			return false;
		}

		return true;
	}

	function findGfSubmitButton($form, formId) {
		var $submit = $form
			.find(
				'#gform_submit_button_' +
					formId +
					', .gform_page_footer button[type="submit"], .gform_page_footer input[type="submit"]'
			)
			.first();

		if (!$submit.length) {
			$submit = $form.find('button.gform_button[type="submit"]').first();
		}

		return $submit;
	}

	function setupLastGfPage($form, $root, formId, isLast) {
		var $host = $form.find('[data-tlth-gf-submit-host]');
		var $footerBtn = $root.find('[data-book-wizard-add-to-cart]');
		var addToCartLabel = i18n.addToCart || 'Ajouter au panier';

		if (!$host.length) {
			$host = $('<span class="sr-only" data-tlth-gf-submit-host aria-hidden="true"></span>');
			$form.append($host);
		}

		if (!isLast) {
			$root.removeClass('is-cart-step');
			return;
		}

		$root.addClass('is-cart-step');
		$form.find('input[name="wc_gforms_next_page"]').val('0');
		$form.find('input[name="wc_gforms_previous_page"]').val('0');

		// Do not set gform_target_page_number to 0 here — that triggers GF confirmation UI.

		$form.find('.gform_confirmation_wrapper, .gform_confirmation_message').remove();

		var $submit = findGfSubmitButton($form, formId);
		if ($submit.length) {
			if ($submit.is('input')) {
				$submit.val(addToCartLabel);
			} else {
				$submit.text(addToCartLabel);
			}
			$submit.attr('type', 'submit');
			$host.empty().append(
				$submit
					.detach()
					.addClass('sr-only book-wizard__gf-submit-native')
					.attr({ tabindex: '-1', 'aria-hidden': 'true' })
			);
			ensureGfSubmitButtonWired($form, formId);
		}

		$footerBtn.text(addToCartLabel);
		ensureWcGfHiddenFields($form, formId);
		syncVariationId($form);
	}

	function getGfSubmitButtonElement($form, formId) {
		var $submit = $form.find('[data-tlth-gf-submit-host]').find('button, input[type="submit"]').first();
		if (!$submit.length) {
			$submit = findGfSubmitButton($form, formId);
		}
		return $submit;
	}

	function ensureGfSubmitButtonWired($form, formId) {
		var $submit = getGfSubmitButtonElement($form, formId);
		if (!$submit.length) {
			return $submit;
		}

		if ($submit.data('tlthGfSubmitWired')) {
			return $submit;
		}

		var el = $submit[0];
		$submit.attr('type', 'submit');
		$submit.data('tlthGfSubmitWired', true);

		$submit.off('click.tlthGfNativeSubmit').on('click.tlthGfNativeSubmit', function (e) {
			if ($form.data('tlthGfPaging') || $form.data('tlthAddToCartInProgress')) {
				return;
			}

			var submission = window.gform && window.gform.submission;
			if (submission && typeof submission.handleButtonClick === 'function') {
				e.preventDefault();
				submission.handleButtonClick(el);
				return false;
			}
		});

		return $submit;
	}

	function triggerGfNativeAddToCart($form, formId) {
		var lastPage = getLastGfPage($form, formId);
		var submission = window.gform && window.gform.submission;
		var formEl = getGfFormElement($form, formId);
		var $submit = ensureGfSubmitButtonWired($form, formId);

		syncGfPagingFields($form, formId, lastPage, '0');
		setGfPageFields($form, formId, lastPage, 0, 'next');

		if ($submit.length && submission && typeof submission.handleButtonClick === 'function') {
			wizardLog('Add to cart: gform.submission.handleButtonClick');
			var clickResult = submission.handleButtonClick($submit[0]);
			if (clickResult && typeof clickResult.then === 'function') {
				return clickResult;
			}
			return $.when();
		}

		if (formEl && submission && typeof submission.submitForm === 'function') {
			wizardLog('Add to cart: gform.submission.submitForm');
			var method = submission.getSubmissionMethod(formEl);
			if (!submission.lockSubmission(formEl)) {
				return $.Deferred().reject('locked').promise();
			}
			return submission
				.submitForm(formEl, submission.SUBMISSION_TYPE_SUBMIT, method)
				.finally(function () {
					submission.unlockSubmission(formEl);
				});
		}

		return $.Deferred().reject('no-native-submit').promise();
	}

	function waitForCartAdded(productId, variationId, countBefore, maxMs) {
		maxMs = maxMs || 15000;
		var deferred = $.Deferred();
		var settled = false;

		function settle(ok) {
			if (settled) {
				return;
			}
			settled = true;
			cleanup();
			deferred.resolve(!!ok);
		}

		function cleanup() {
			clearInterval(pollId);
			clearTimeout(timeoutId);
			$(document.body).off('added_to_cart.tlthWizardWait', onAdded);
		}

		function onAdded() {
			settle(true);
		}

		$(document.body).on('added_to_cart.tlthWizardWait', onAdded);

		var pollId = setInterval(function () {
			fetchCartSnapshot(productId, variationId).done(function (snap) {
				if (snap && snap.success && snap.data && snap.data.count > countBefore) {
					settle(true);
				}
			});
		}, 400);

		var timeoutId = setTimeout(function () {
			settle(false);
		}, maxMs);

		return deferred.promise();
	}

	function normalizeAttributeFieldName(name) {
		if (!name || name.indexOf('attribute_') !== 0) {
			return name;
		}

		// WooCommerce may suffix field names with _{form_hash} on variable product forms.
		return name.replace(/_[a-f0-9]{6,16}$/i, '');
	}

	function getSelectedVariationAttributes($form) {
		var attrs = {};

		$form.find('select[name^="attribute_"]').each(function () {
			var $el = $(this);
			var val = $el.val();
			if (val) {
				attrs[normalizeAttributeFieldName($el.attr('name'))] = val;
			}
		});

		$form.find('input[type="radio"][name^="attribute_"]:checked').each(function () {
			var $el = $(this);
			attrs[normalizeAttributeFieldName($el.attr('name'))] = $el.val();
		});

		return attrs;
	}

	function findMatchingVariationId($form) {
		var variations = $form.data('product_variations');

		if (typeof variations === 'string') {
			try {
				variations = JSON.parse(variations);
			} catch (err) {
				variations = [];
			}
		}

		if (!variations || !variations.length) {
			return null;
		}

		var attrs = getSelectedVariationAttributes($form);
		var keys = Object.keys(attrs);

		if (!keys.length) {
			return null;
		}

		for (var i = 0; i < variations.length; i++) {
			var variation = variations[i];
			if (!variation || !variation.variation_id) {
				continue;
			}
			if (variation.variation_is_active === false || variation.variation_is_visible === false) {
				continue;
			}

			var matches = true;
			for (var k = 0; k < keys.length; k++) {
				var key = keys[k];
				var varVal = variation.attributes[key];

				if (varVal !== undefined && varVal !== '' && varVal !== attrs[key]) {
					matches = false;
					break;
				}
			}

			if (matches) {
				return String(variation.variation_id);
			}
		}

		return null;
	}

	function syncVariationId($form) {
		var deferred = $.Deferred();
		var $input = $form.find('input[name="variation_id"]');
		var current = $input.val();

		if (current && current !== '0') {
			deferred.resolve(current);
			return deferred.promise();
		}

		var stored = $form.data('tlthVariationId');
		if (stored) {
			$input.val(stored);
			deferred.resolve(String(stored));
			return deferred.promise();
		}

		var matched = findMatchingVariationId($form);
		if (matched) {
			$input.val(matched);
			$form.data('tlthVariationId', matched);
			deferred.resolve(matched);
			return deferred.promise();
		}

		var timeout = window.setTimeout(function () {
			$form.off('found_variation.tlthSync found_variation');
			var fallback = findMatchingVariationId($form) || $form.find('input[name="variation_id"]').val();
			if (fallback && fallback !== '0') {
				$input.val(fallback);
				$form.data('tlthVariationId', fallback);
				deferred.resolve(String(fallback));
			} else {
				deferred.reject();
			}
		}, 1500);

		$form.one('found_variation.tlthSync found_variation', function (event, variation) {
			window.clearTimeout(timeout);
			if (variation && variation.variation_id) {
				$input.val(variation.variation_id);
				$form.data('tlthVariationId', variation.variation_id);
				deferred.resolve(String(variation.variation_id));
			} else {
				deferred.reject();
			}
		});

		$form.trigger('check_variations');
		return deferred.promise();
	}

	function showWizardError($form, message) {
		$form.find('.book-wizard__error').remove();
		var $heading = $form.find('[data-book-wizard-heading]');
		if ($heading.length) {
			$heading.after(
				'<p class="book-wizard__error text-center text-red text-sm mb-4">' + message + '</p>'
			);
		}
	}

	function unlockGfSubmission($form, formId) {
		var formEl = getGfFormElement($form, formId);
		if (window.gform && window.gform.submission && formEl && typeof window.gform.submission.unlockSubmission === 'function') {
			window.gform.submission.unlockSubmission(formEl);
		}
	}

	function getGfPageCountFromDom($form, formId) {
		var max = 0;

		$form.find('#gform_' + formId + ' .gform_page, .gform_page').each(function () {
			var match = ($(this).attr('id') || '').match(/gform_page_\d+_(\d+)/);
			if (match) {
				max = Math.max(max, parseInt(match[1], 10) || 0);
			}
		});

		return max;
	}

	function getLastGfPage($form, formId) {
		var configMax = parseInt(config.gfPageCount, 10) || 1;
		var domMax = 0;
		var tracked = 0;

		if ($form && $form.length) {
			formId = formId || getGfFormId($form) || config.formId;
			domMax = getGfPageCountFromDom($form, formId);
			tracked = parseInt($form.data('tlthWizardGfPage'), 10) || 0;
		}

		return Math.max(configMax, domMax, tracked, 1);
	}

	function resolveSubmitSourcePage($form, formId) {
		return getLastGfPage($form, formId);
	}

	function safeGfReinit(formId, page) {
		try {
			if (window.gform && typeof window.gform.initializeOnLoaded === 'function') {
				window.gform.initializeOnLoaded(function () {
					$(document).trigger('gform_post_render', [formId, page]);
				});
				return;
			}
		} catch (err) {
			wizardDebug('initializeOnLoaded failed', err);
		}

		$(document).trigger('gform_post_render', [formId, page]);
	}

	function showGfPageInWizard($form, formId, page) {
		var pageNum = parseInt(page, 10) || 1;
		var $pages = $form.find('#gform_' + formId + ' .gform_page');

		if (!$pages.length) {
			$pages = $form.find('.gform_page');
		}

		$pages.each(function () {
			var $page = $(this);
			var match = ($page.attr('id') || '').match(/gform_page_\d+_(\d+)/);
			var num = match ? parseInt(match[1], 10) : 0;

			if (num === pageNum) {
				$page.show();
			} else {
				$page.hide();
			}
		});
	}

	function clearWizardLoading($form, $root) {
		($root || $form.closest('[data-book-wizard]')).removeClass('book-wizard-loading');
		$form.removeClass('book-wizard-loading');
	}

	function setWizardLoading($form, $root) {
		($root || $form.closest('[data-book-wizard]')).addClass('book-wizard-loading');
		$form.addClass('book-wizard-loading');
	}

	function refreshGfAfterMarkupChange($form, $root, formId, page) {
		refreshGfDom($form);
		overrideWcGfPagingHandlers($form);
		patchGfApplyRulesSafe();
		unlockGfSubmission($form, formId);

		if (page) {
			$form.data('tlthWizardGfPage', page);
			syncGfPagingFields($form, formId, page, '0');
			showGfPageInWizard($form, formId, page);
		}

		if ($root && $root.hasClass('is-last-step')) {
			setupLastGfPage($form, $root, formId, true);
		}

		safeGfReinit(formId, page || getEffectiveGfSourcePage($form, formId));
	}

	function handleAddToCartValidationFailure($form, $root, formId, html) {
		var lastPage = getLastGfPage($form, formId);
		var normalized = normalizeGfAjaxResponse(typeof html === 'string' ? html : '');
		var fragment = '';

		if (normalized.parsed && normalized.parsed.status === 'validation_failed') {
			fragment = normalized.parsed.html || normalized.html || '';
		} else if (html && html.indexOf('gform') !== -1) {
			if (html.indexOf('<') !== -1) {
				var $doc = parseGfResponseDocument(html);
				fragment = extractGformHtml($doc, formId) || '';
			}
			if (!fragment && html.indexOf('gform_wrapper') !== -1) {
				fragment = html;
			}
		}

		if (fragment && fragment.indexOf('gform') !== -1) {
			replaceGformMarkup($form, fragment);
		}

		syncGfPagingFields($form, formId, lastPage, '0');
		refreshGfAfterMarkupChange($form, $root, formId, lastPage);
		$form.trigger('tlthGfPageChanged', [lastPage]);
		scrollToWizard();

		var $firstError = $form.find('.gfield_error, .gfield_validation_message').filter(':visible').first();
		if ($firstError.length) {
			$firstError[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
		}
	}

	function upsertHiddenInput($form, name, value) {
		var $input = $form.find('input[name="' + name + '"]');
		if (!$input.length) {
			$input = $('<input type="hidden" />').attr('name', name).appendTo($form);
		}
		$input.val(value);
	}

	function revealAllGfPagesForSubmit($form, formId) {
		var $pages = $form.find('#gform_' + formId + ' .gform_page, .gform_page');
		if (!$pages.length) {
			return;
		}

		$pages.show();
		$pages.find(':input:disabled').prop('disabled', false);
	}

	function getGfWrapper($form, formId) {
		var $wrap = $form.find('#gform_wrapper_' + formId).first();
		if (!$wrap.length) {
			$wrap = $form.find('.gform_variation_wrapper').first();
		}
		if (!$wrap.length) {
			$wrap = $form.find('.gform_wrapper').first();
		}
		return $wrap;
	}

	function syncGfStateBeforeSubmit($form, formId) {
		var stateName = 'state_' + formId;
		var $wrap = getGfWrapper($form, formId);
		var $state = $wrap.find('input[name="' + stateName + '"]');

		if (!$state.length) {
			$state = $form.find('input[name="' + stateName + '"]');
		}
		if (!$state.length) {
			$state = $('<input type="hidden" />').attr('name', stateName).appendTo($form);
		}

		if (window.gform && window.gform.utils) {
			if (typeof window.gform.utils.getFormState === 'function') {
				var encoded = window.gform.utils.getFormState(formId);
				if (encoded) {
					$state.val(encoded);
				}
			} else if (
				typeof window.gform.utils.getStateObject === 'function' &&
				typeof window.gform.utils.encodeState === 'function'
			) {
				var stateObj = window.gform.utils.getStateObject(formId);
				if (stateObj) {
					$state.val(window.gform.utils.encodeState(stateObj));
				}
			}
		}

		if (
			!$state.val() &&
			window.gform &&
			window.gform.state &&
			window.gform.state.data &&
			window.gform.state.data[formId]
		) {
			var stateData = window.gform.state.data[formId];
			if (typeof stateData === 'string') {
				$state.val(stateData);
			}
		}

		$(document).trigger('gform_pre_submission', [formId]);
	}

	function syncVariationFieldsToDom($form) {
		var attrs = getSelectedVariationAttributes($form);

		Object.keys(attrs).forEach(function (key) {
			if (attrs[key]) {
				upsertHiddenInput($form, key, attrs[key]);
			}
		});

		var variationId =
			$form.find('input[name="variation_id"]').val() ||
			$form.data('tlthVariationId') ||
			findMatchingVariationId($form);

		if (variationId && variationId !== '0') {
			$form.find('input[name="variation_id"]').val(variationId);
			$form.data('tlthVariationId', variationId);
		}
	}

	function ensureGfNonceInFormData(fd, $form, formId) {
		var nonceName = '_gform_submit_nonce_' + formId;
		var $nonce = $form.find('input[name="' + nonceName + '"]');
		if ($nonce.length) {
			fd.set(nonceName, $nonce.val());
		}
	}

	function prepareGfFormForAddToCart($form, formId) {
		revealAllGfPagesForSubmit($form, formId);
		syncGfStateBeforeSubmit($form, formId);
		syncVariationFieldsToDom($form);
		ensureWcGfHiddenFields($form, formId);

		if (!$form.find('input[name="gform_unique_id"]').length) {
			upsertHiddenInput($form, 'gform_unique_id', '');
		}

		upsertHiddenInput($form, 'gform_field_values', $form.find('input[name="gform_field_values"]').val() || '');
	}

	function ensureWcGfHiddenFields($form, formId) {
		var productId = config.productId || $form.find('input[name="add-to-cart"]').val();
		var lastPage = getLastGfPage($form, formId);
		var isVariable = $form.hasClass('variations_form');

		syncGfPagingFields($form, formId, lastPage, '0');

		if (productId) {
			upsertHiddenInput($form, 'add-to-cart', productId);
			upsertHiddenInput($form, 'product_id', productId);
		}

		upsertHiddenInput($form, 'gform_form_id', formId);
		upsertHiddenInput($form, 'wc_gforms_form_id', formId);
		upsertHiddenInput($form, 'gform_old_submit', formId);
		$form.find('input[name="gform_submit"]').remove();
		upsertHiddenInput($form, 'wc_gforms_next_page', '0');
		upsertHiddenInput($form, 'wc_gforms_previous_page', '0');
		upsertHiddenInput($form, 'wc_gforms_product_type', isVariable ? 'variable' : 'simple');
		upsertHiddenInput($form, 'is_submit_' + formId, '1');
		upsertHiddenInput($form, 'quantity', getAddToCartQuantity($form));
	}

	function forEachGfField($gfRoot, callback) {
		$gfRoot.find('input, select, textarea').each(function () {
			var $input = $(this);
			var name = $input.attr('name');
			if (!name || name === 'gform_ajax') {
				return;
			}

			var type = ($input.attr('type') || '').toLowerCase();
			if ((type === 'checkbox' || type === 'radio') && !$input.prop('checked')) {
				return;
			}
			if (type === 'file') {
				return;
			}

			callback(name, $input.val());
		});
	}

	function mergeNestedGfIntoFormData(fd, $form, formId) {
		var cartEl = $form[0];
		var gfEl = getGfFormElement($form, formId);

		if (!gfEl || gfEl === cartEl) {
			return fd;
		}

		if (gfEl.tagName === 'FORM') {
			var gfFd = new FormData(gfEl);
			gfFd.forEach(function (value, key) {
				fd.set(key, value);
			});
			return fd;
		}

		forEachGfField($(gfEl), function (name, value) {
			fd.set(name, value);
		});

		return fd;
	}

	function appendVariationFieldsToFormData(fd, $form) {
		var attrs = getSelectedVariationAttributes($form);
		var attrNames = {};

		$form.find('[name^="attribute_"]').each(function () {
			attrNames[this.name] = true;
		});

		fd.forEach(function (value, key) {
			if (key.indexOf('attribute_') === 0) {
				attrNames[key] = true;
			}
		});

		Object.keys(attrNames).forEach(function (key) {
			fd.delete(key);
		});

		Object.keys(attrs).forEach(function (key) {
			if (attrs[key]) {
				fd.set(key, attrs[key]);
			}
		});

		var variationId =
			$form.find('input[name="variation_id"]').val() ||
			$form.data('tlthVariationId') ||
			findMatchingVariationId($form);

		if (variationId && variationId !== '0') {
			fd.set('variation_id', variationId);
		}

		return fd;
	}

	function serializeWizardForm($form, formId) {
		var data = $form.serializeArray();
		var $wrap = getGfWrapper($form, formId);

		if ($wrap.length) {
			forEachGfField($wrap, function (name, value) {
				upsertFormField(data, name, value);
			});
		}

		return data;
	}

	function cloneFormData(fd) {
		var copy = new FormData();
		fd.forEach(function (value, key) {
			copy.append(key, value);
		});
		return copy;
	}

	function formDataFromFieldList(data) {
		var fd = new FormData();
		data.forEach(function (item) {
			fd.append(item.name, item.value);
		});
		return fd;
	}

	function countFormDataInputs(fd, prefix) {
		var count = 0;
		fd.forEach(function (value, key) {
			if (key.indexOf(prefix) === 0) {
				count++;
			}
		});
		return count;
	}

	function applyAddToCartFieldsToFormData(fd, $form, formId) {
		var sourcePage = resolveSubmitSourcePage($form, formId);

		fd.delete('action');
		fd.delete('nonce');
		fd.delete('gform_ajax');
		fd.delete('gform_submit');
		fd.set('gform_target_page_number_' + formId, '0');
		fd.set('gform_source_page_number_' + formId, String(sourcePage));
		fd.set('is_submit_' + formId, '1');
		fd.set('gform_old_submit', String(formId));
		fd.set('gform_form_id', String(formId));
		fd.set('wc_gforms_form_id', String(formId));
		fd.set('wc_gforms_next_page', '0');
		fd.set('wc_gforms_previous_page', '0');
		fd.set('wc_gforms_product_type', $form.hasClass('variations_form') ? 'variable' : 'simple');

		var productId = config.productId || $form.find('input[name="add-to-cart"]').val();
		if (productId) {
			fd.set('add-to-cart', productId);
			fd.set('product_id', productId);
		}

		appendVariationFieldsToFormData(fd, $form);
		fd.set('quantity', String(getAddToCartQuantity($form)));

		return fd;
	}

	function appendGfFileFieldsToFormData(fd, $form, formId) {
		getGfWrapper($form, formId).find('input[type="file"]').each(function () {
			var $input = $(this);
			var name = $input.attr('name');
			if (!name || !$input[0].files || !$input[0].files.length) {
				return;
			}
			fd.set(name, $input[0].files[0]);
		});
	}

	function buildAddToCartFormData($form, formId) {
		prepareGfFormForAddToCart($form, formId);

		var fd = formDataFromFieldList(serializeWizardForm($form, formId));
		appendGfFileFieldsToFormData(fd, $form, formId);
		appendVariationFieldsToFormData(fd, $form);
		ensureGfNonceInFormData(fd, $form, formId);

		return applyAddToCartFieldsToFormData(fd, $form, formId);
	}

	function stripGformAjaxFromFormData(fd) {
		var keys = [];
		fd.forEach(function (value, key) {
			if (key === 'gform_ajax') {
				keys.push(key);
			}
		});
		keys.forEach(function (key) {
			fd.delete(key);
		});
	}

	function buildAddToCartServerFormData($form, formId, snapshotFd) {
		var fd = snapshotFd ? cloneFormData(snapshotFd) : buildAddToCartFormData($form, formId);
		stripGformAjaxFromFormData(fd);
		fd.set('action', 'tlth_book_wizard_add_to_cart');
		fd.set('nonce', config.nonce || '');
		fd.set('product_id', String(config.productId || ''));
		return fd;
	}

	function applyGfFieldErrorsFromServer($form, formId, fieldErrors) {
		if (!fieldErrors || !fieldErrors.length) {
			return;
		}

		$form.find('.gfield_error').removeClass('gfield_error');
		$form.find('.gfield_validation_message').remove();

		fieldErrors.forEach(function (err) {
			if (!err || !err.id) {
				return;
			}

			var $field = $form.find('#field_' + formId + '_' + err.id).first();
			if (!$field.length) {
				$field = $form.find('#input_' + formId + '_' + err.id).closest('.gfield').first();
			}

			if (!$field.length) {
				return;
			}

			$field.addClass('gfield_error');
			if (err.message) {
				$field.append(
					'<motion.div class="gfield_description validation_message gfield_validation_message">' +
						err.message +
						'</motion.div>'
				);
			} else if (err.label) {
				$field.append(
					'<motion.div class="gfield_description validation_message gfield_validation_message">' +
						err.label +
						'</motion.div>'
				);
			}
		});
	}

	function logAddToCartSnapshot(label, fd, formId) {
		var stateVal = fd.get('state_' + formId) || '';
		wizardLog(label, {
			gfInputCount: countFormDataInputs(fd, 'input_'),
			hasState: !!stateVal,
			stateLength: String(stateVal).length,
			sourcePage: fd.get('gform_source_page_number_' + formId),
			variationId: fd.get('variation_id'),
		});
	}

	function submitAddToCart($form, $root, formId) {
		if (!validateGfFormForSubmit($form, formId)) {
			scrollToWizard();
			return;
		}

		syncVariationId($form)
			.done(function () {
				runAddToCartSubmit($form, $root, formId);
			})
			.fail(function () {
				wizardLog('Add to cart blocked: could not resolve variation_id');
				showWizardError(
					$form,
					i18n.selectVariation || 'Choisis toutes les options du produit pour continuer.'
				);
				scrollToWizard();
			});
	}

	function runAddToCartSubmit($form, $root, formId) {
		var failMsg = i18n.addToCartFailed || 'Le produit n\'a pas pu être ajouté au panier. Vérifie les champs et réessaie.';
		var productId = config.productId;
		var variationId = $form.find('input[name="variation_id"]').val() || $form.data('tlthVariationId');

		$form.data('tlthAddToCartInProgress', true);
		setWizardLoading($form, $root);

		function finish() {
			$form.removeData('tlthAddToCartInProgress');
			clearWizardLoading($form, $root);
			unlockGfSubmission($form, formId);
		}

		function tryAjaxFallback(snapshotFd) {
			submitAddToCartRequest($form, $root, formId, failMsg, snapshotFd).always(finish);
		}

		function afterSnapshot() {
			var snapshotFd = cloneFormData(buildAddToCartFormData($form, formId));
			logAddToCartSnapshot('Add to cart snapshot (before submit)', snapshotFd, formId);

			if (countFormDataInputs(snapshotFd, 'input_') < 1) {
				wizardLog('Add to cart: snapshot has no GF field values');
				validateGfFormForSubmit($form, formId);
				showWizardError($form, failMsg);
				scrollToWizard();
				finish();
				return;
			}

			tryAjaxFallback(snapshotFd);
		}

		$.when(fetchCartSnapshot(productId, variationId)).always(function () {
			afterSnapshot();
		});
	}

	function submitAddToCartViaProductPage($form, $root, formId, productId, variationId, countBefore, snapshotFd) {
		var deferred = $.Deferred();
		var pageUrl = config.pageUrl;

		if (!pageUrl || typeof window.fetch !== 'function') {
			return deferred.resolve(false).promise();
		}

		var fd = snapshotFd ? cloneFormData(snapshotFd) : buildAddToCartFormData($form, formId);
		fd.delete('action');
		fd.delete('nonce');

		logAddToCartSnapshot('Add to cart: POST product page', fd, formId);

		window
			.fetch(pageUrl, {
				method: 'POST',
				body: fd,
				credentials: 'same-origin',
			})
			.then(function (response) {
				return response.text();
			})
			.then(function (html) {
				function resolveFromCart() {
					fetchCartSnapshot(productId, variationId).done(function (snapRes) {
						var snap = snapRes && snapRes.success && snapRes.data ? snapRes.data : {};
						var added =
							!!snap.has_product ||
							(typeof snap.count === 'number' && snap.count > countBefore);

						if (added) {
							showCartConfirmation($root, $form);
							$(document.body).trigger('wc_fragment_refresh');
							deferred.resolve(true);
							return;
						}

						deferred.resolve(false);
					});
				}

				if (isWooCommerceCartNoticeHtml(html)) {
					wizardLog('Add to cart: product page returned cart success notice');
					resolveFromCart();
					return;
				}

				var hasGfErr = responseHasGfValidation(html, formId);
				var hasWcErr = responseHasWooCommerceError(html);

				if (hasGfErr || hasWcErr) {
					wizardLog('Add to cart: product page returned validation errors', {
						hasGfErr: hasGfErr,
						hasWcErr: hasWcErr,
					});
					handleAddToCartValidationFailure($form, $root, formId, html);
					deferred.resolve(false);
					return;
				}

				resolveFromCart();
			})
			.catch(function (err) {
				wizardLog('Add to cart: product page POST failed', err);
				deferred.resolve(false);
			});

		return deferred.promise();
	}

	function submitAddToCartRequest($form, $root, formId, failMsg, snapshotFd) {
		failMsg =
			failMsg || i18n.addToCartFailed || 'Le produit n\'a pas pu être ajouté au panier. Vérifie les champs et réessaie.';

		return $.ajax({
			url: config.ajaxUrl || window.ajaxurl,
			type: 'POST',
			data: buildAddToCartServerFormData($form, formId, snapshotFd),
			processData: false,
			contentType: false,
			dataType: 'json',
		}).then(function (res) {
			if (!res || !res.success) {
				wizardLog('Add to cart: server error', res);
				showWizardError($form, failMsg);
				return;
			}

			var data = res.data || {};
			wizardDebug('Add to cart server result', data);

			if (data.added) {
				showCartConfirmation($root, $form);
				$(document.body).trigger('wc_fragment_refresh');
				return;
			}

			if (data.validation_failed || (data.gf && data.gf.status === 'validation_failed')) {
				wizardLog('Add to cart: server validation failed', data);
				if (data.debug) {
					wizardLog('Add to cart debug', data.debug);
				}
				if (data.gf && data.gf.field_errors && data.gf.field_errors.length) {
					applyGfFieldErrorsFromServer($form, formId, data.gf.field_errors);
				} else {
					validateGfFormForSubmit($form, formId);
				}
				var lastPage = getLastGfPage($form, formId);
				refreshGfAfterMarkupChange($form, $root, formId, lastPage);
				if (data.reason === 'incomplete_pages') {
					wizardLog('Add to cart: GF multipage submit incomplete', data.gf);
				}
				if (data.gf_validation_failed || (data.gf && data.gf.messages && data.gf.messages.length)) {
					showWizardError($form, failMsg);
				} else if (data.wc_validation_failed && data.reason !== 'missing_variation_id') {
					if (data.wc_errors && data.wc_errors.length) {
						showWizardError($form, data.wc_errors[0]);
					} else {
						var attrs = (data.debug && data.debug.attribute_keys) || [];
						var hasNormalizedAttrs = attrs.some(function (key) {
							return key.indexOf('attribute_') === 0 && !/_([a-f0-9]{6,16})$/i.test(key);
						});
						if (!hasNormalizedAttrs && attrs.length) {
							wizardLog('Add to cart: attribute keys may be invalid', attrs);
						}
						showWizardError(
							$form,
							i18n.selectVariation || 'Choisis toutes les options du produit pour continuer.'
						);
					}
				} else {
					showWizardError($form, failMsg);
				}
				scrollToWizard();
				return;
			}

			wizardLog('Add to cart: product not added', data);
			if (data.debug) {
				wizardLog('Add to cart debug', data.debug);
			}
			if (data.wc_errors && data.wc_errors.length) {
				wizardLog('Add to cart WC errors', data.wc_errors);
			}
			if (data.reason === 'add_to_cart_failed' && data.wc_errors && data.wc_errors.length) {
				showWizardError($form, data.wc_errors[0]);
			} else {
				showWizardError($form, failMsg);
			}
		}, function (xhr) {
			var body = xhr && xhr.responseText ? xhr.responseText : '';

			if (body && body.indexOf('gform_confirmation') !== -1) {
				wizardLog(
					'Add to cart: server returned GF confirmation HTML instead of JSON (gform_ajax postback leaked)'
				);
				showWizardError($form, failMsg);
				scrollToWizard();
				return;
			}

			try {
				var parsed = typeof body === 'string' && body.trim().charAt(0) === '{' ? JSON.parse(body) : null;
				if (parsed && parsed.success) {
					if (parsed.data && parsed.data.added) {
						showCartConfirmation($root, $form);
						$(document.body).trigger('wc_fragment_refresh');
						return;
					}
					wizardLog('Add to cart: parsed JSON from non-json response', parsed.data);
				}
			} catch (err) {
				wizardDebug('Add to cart: could not parse error response', err);
			}

			wizardLog('Add to cart server request failed', xhr && xhr.status, body ? body.substring(0, 200) : '');
			showWizardError($form, failMsg);
		});
	}

	function bindAddToCartHandler($form, $root, formId) {
		$root.off('click.tlthAddToCart').on('click.tlthAddToCart', '[data-book-wizard-add-to-cart]', function (e) {
			e.preventDefault();
			if (!$root.hasClass('is-last-step')) {
				return;
			}
			submitAddToCart($form, $root, formId);
		});

		$form.off('submit.tlthAddToCart').on('submit.tlthAddToCart', function (e) {
			if ($form.data('tlthGfPaging')) {
				return;
			}

			if (!$root.hasClass('is-last-step')) {
				return;
			}

			// Let GF 2.9+ handle submission when triggered from the native submit button.
			if (e.originalEvent && e.originalEvent.submitter) {
				return;
			}

			e.preventDefault();
			e.stopPropagation();
			submitAddToCart($form, $root, formId);
		});
	}

	function buildFooter($root, $form, $variationWrap) {
		dedupeWizardFooter($root);

		if (!$root.find('[data-book-wizard-footer]').length) {
			var $footer = $(
				'<div class="book-wizard-footer" data-book-wizard-footer>' +
					'<div class="book-wizard-footer__nav">' +
						'<p class="book-wizard-footer__price" data-book-wizard-price></p>' +
						'<div class="book-wizard-footer__controls">' +
							'<button type="button" class="book-wizard-nav book-wizard-nav--prev btn" data-book-wizard-prev>' +
								(i18n.prev || 'Précédent') +
							'</button>' +
							'<div class="book-wizard-footer__nav-actions">' +
								'<button type="button" class="book-wizard-nav book-wizard-nav--next btn" data-book-wizard-next>' +
									(i18n.next || 'Suivant') +
								'</button>' +
								'<button type="button" class="book-wizard-nav book-wizard-nav--add-to-cart btn hidden" data-book-wizard-add-to-cart>' +
									(i18n.addToCart || 'Ajouter au panier') +
								'</button>' +
							'</div>' +
						'</div>' +
					'</div>' +
				'</div>'
			);

			$root.append($footer);
		}

		syncFooterPrice($root, $form, $variationWrap);
	}

	function findMatchingVariation($form) {
		var attrs = getSelectedVariationAttributes($form);
		var keys = Object.keys(attrs);

		if (!keys.length) {
			return null;
		}

		var variations = getProductVariations($form);

		for (var i = 0; i < variations.length; i++) {
			var variation = variations[i];

			if (!variation || !variation.variation_id) {
				continue;
			}
			if (variation.variation_is_active === false || variation.variation_is_visible === false) {
				continue;
			}
			if (!isVariationInStock(variation)) {
				continue;
			}
			if (variationMatchesAttributes(variation, attrs)) {
				return variation;
			}
		}

		return null;
	}

	function getVariationPriceHtml($form, $variationWrap) {
		var variation = findMatchingVariation($form);

		if (variation && variation.price_html) {
			return variation.price_html;
		}

		var $price = $variationWrap.find('.woocommerce-variation-price').first();
		if ($price.length && $price.html().trim()) {
			return $price.html();
		}

		return '';
	}

	function syncFooterPrice($root, $form, $variationWrap) {
		var $priceEl = $root.find('[data-book-wizard-price]').first();

		if (!$priceEl.length) {
			return;
		}

		var priceHtml = getVariationPriceHtml($form, $variationWrap);

		if (priceHtml) {
			$priceEl.html(formatPriceLine(priceHtml));
			return;
		}

		$priceEl.empty();
	}

	function updateProgress($progress, current, total) {
		var $compact = $progress.find('[data-book-wizard-progress-compact]');
		$compact.find('.book-wizard-progress__compact-dot').text(current + '/' + total);

		var stepOfTpl = i18n.stepOf || 'Étape %1$d de %2$d';
		$compact.attr(
			'aria-label',
			stepOfTpl.replace('%1$d', String(current)).replace('%2$d', String(total))
		);

		$progress.find('.book-wizard-progress__track .book-wizard-progress__item').each(function () {
			var step = parseInt($(this).attr('data-step'), 10);
			var isCurrent = step === current;
			$(this)
				.toggleClass('is-complete', step < current)
				.toggleClass('is-current', isCurrent);
			$(this)
				.find('.book-wizard-progress__dot')
				.text(isCurrent ? String(step) : '');
		});
	}

	function formatPriceLine(priceHtml) {
		var tpl = i18n.priceTpl || "Le livre revient à %s l'exemplaire.";
		var text = $('<div>').html(priceHtml).text().trim();
		return tpl.replace('%s', '<strong>' + text + '</strong>');
	}

	function hideGfProgress($gfWrapper) {
		$gfWrapper
			.find(
				'.gf_progressbar_wrapper, .gf_progressbar, .gform_page_steps, .gf_page_steps, .percentbar_blue, .gf_step_page_name'
			)
			.addClass('book-wizard__gf-native-hidden');
	}

	function bindNativeGfButtonIntercept($root, $form, formId) {
		$root.off('click.tlthGfNative');

		$root.on(
			'click.tlthGfNative',
			'.gform_next_button, input.gform_next_button, .gform_previous_button, input.gform_previous_button',
			function (e) {
				if (!$form.data('wizardInitialized')) {
					return;
				}

				e.preventDefault();
				e.stopImmediatePropagation();

				var isNext = $(this).is('.gform_next_button') || $(this).hasClass('gform_next_button');
				navigateGfPage($form, isNext ? 'next' : 'prev', formId);

				return false;
			}
		);
	}

	function getGfFormId($form) {
		var id =
			$form.find('input[name="wc_gforms_form_id"]').val() ||
			$form.find('input[name="gform_form_id"]').val() ||
			config.formId;
		return parseInt(id, 10) || 0;
	}

	function getGfSourcePageFromDom($form, formId) {
		var name = 'gform_source_page_number_' + formId;
		var $sources = $form.find('#' + name + ', input[name="' + name + '"]');
		var page = 0;

		$sources.each(function () {
			var v = parseInt($(this).val(), 10) || 0;
			if (v > page) {
				page = v;
			}
		});

		return page || 1;
	}

	function getEffectiveGfSourcePage($form, formId) {
		var variationCount = parseInt($form.data('tlthVariationCount'), 10) || 0;
		var wizardStep = parseInt($form.attr('data-current-step'), 10) || 0;

		if (variationCount && wizardStep > variationCount) {
			return wizardStep - variationCount;
		}

		var tracked = parseInt($form.data('tlthWizardGfPage'), 10);
		if (tracked) {
			return tracked;
		}

		return getGfSourcePageFromDom($form, formId);
	}

	function syncGfPagingFields($form, formId, sourcePage, targetPage) {
		var sourceName = 'gform_source_page_number_' + formId;
		var targetName = 'gform_target_page_number_' + formId;
		var sourceVal = String(sourcePage || 1);
		var targetVal = targetPage === undefined || targetPage === null ? '0' : String(targetPage);

		$form.find('input[name="' + sourceName + '"]').remove();
		$form.find('input[name="' + targetName + '"]').remove();

		$('<input type="hidden" />')
			.attr({ type: 'hidden', id: sourceName, name: sourceName, value: sourceVal })
			.appendTo($form);
		$('<input type="hidden" />')
			.attr({ type: 'hidden', id: targetName, name: targetName, value: targetVal })
			.appendTo($form);
	}

	function getGfCurrentPage($form, formId) {
		return getEffectiveGfSourcePage($form, formId);
	}

	function normalizeGfAjaxResponse(response) {
		if (!response || typeof response !== 'string') {
			return { parsed: null, html: response || '' };
		}

		var trimmed = response.trim();
		if (trimmed.charAt(0) !== '{' && trimmed.charAt(0) !== '[') {
			return { parsed: null, html: response };
		}

		try {
			var parsed = JSON.parse(trimmed);
			if (parsed && parsed.success === false && parsed.data) {
				parsed = typeof parsed.data === 'string' ? JSON.parse(parsed.data) : parsed.data;
			}
			return {
				parsed: parsed,
				html: parsed && (parsed.html || parsed.data) ? parsed.html || parsed.data : '',
			};
		} catch (err) {
			wizardDebug('GF paging: JSON parse error', err);
			return { parsed: null, html: response };
		}
	}

	function refreshGfDom($form) {
		$form.find('.gform_wrapper').first().addClass('book-wizard__gform');
		hideGfProgress($form.find('.gform_wrapper').first());
	}

	function overrideWcGfPagingHandlers($form) {
		$form.find('.gform_next_button, .gform_previous_button').off('click');
	}

	function validateGfCurrentPage(formId) {
		if (window.gform && window.gform.utils && typeof window.gform.utils.validateCurrentPage === 'function') {
			return window.gform.utils.validateCurrentPage(formId);
		}
		if (window.gform && window.gform.utils && typeof window.gform.utils.validateForm === 'function') {
			return window.gform.utils.validateForm(formId);
		}
		if (window.gform && typeof window.gform.validateForm === 'function') {
			return window.gform.validateForm(formId);
		}
		if (typeof window['gf_validate_' + formId] === 'function') {
			return window['gf_validate_' + formId]();
		}
		return true;
	}

	function upsertFormField(data, name, value) {
		var strVal = String(value);
		var i = data.length;

		// Remove every duplicate — PHP uses the last value; a trailing 0 would final-submit the form.
		while (i--) {
			if (data[i].name === name) {
				data.splice(i, 1);
			}
		}

		data.push({ name: name, value: strVal });
	}

	function isConfirmationForForm(fragment, formId) {
		if (!fragment || typeof fragment !== 'string') {
			return false;
		}

		var $frag = $('<div>').html(fragment);
		return $frag.find('#gform_confirmation_wrapper_' + formId).length > 0;
	}

	function bindGfAjaxSubmit($form, onPageChange) {
		$form.off('submit.tlthGfPaging').on('submit.tlthGfPaging', function (e) {
			if (!$form.data('tlthGfPaging')) {
				return;
			}

			var formId = getGfFormId($form);

			e.preventDefault();
			e.stopPropagation();

			var pagingMeta = $form.data('tlthGfPagingMeta') || {};
			$form.removeData('tlthGfPaging');
			$form.removeData('tlthGfPagingMeta');

			requestGfPageChange($form, formId, pagingMeta, onPageChange);
		});
	}

	function requestGfPageChange($form, formId, pagingMeta, onPageChange) {
		var $card = $form.closest('.book-wizard-card');
		$form.addClass('book-wizard-loading');
		$card.addClass('book-wizard-loading');

		var hasState = !!$form.find('input[name="state_' + formId + '"]').val();
		wizardDebug('GF paging start', {
			formId: formId,
			pagingMeta: pagingMeta,
			hasStateField: hasState,
			variationId: $form.find('input[name="variation_id"]').val(),
		});

		function endLoading() {
			$form.removeClass('book-wizard-loading');
			$card.removeClass('book-wizard-loading');
			unlockGfSubmission($form, formId);
		}

		function tryPagePost() {
			wizardDebug('GF paging: fallback to product page POST');
			$.ajax({
				url: config.pageUrl || config.ajaxUrl,
				type: 'POST',
				data: buildGfPagingPayload($form, formId, pagingMeta, 'page'),
				dataType: 'text',
			})
				.done(function (response) {
					wizardDebug('GF paging: page POST response length', response ? response.length : 0);
					if (!handleGfPagingResponse($form, response, formId, pagingMeta, onPageChange)) {
						wizardDebug('GF paging: page POST did not update the form');
					}
				})
				.fail(function (xhr) {
					wizardDebug('GF paging: page POST failed', xhr.status, xhr.statusText);
				})
				.always(function () {
					endLoading();
					scrollToWizard();
				});
		}

		$.ajax({
			url: config.ajaxUrl || window.ajaxurl,
			type: 'POST',
			data: buildGfPagingPayload($form, formId, pagingMeta, 'wizard'),
			dataType: 'text',
		})
			.done(function (response) {
				wizardDebug('GF paging: wizard ajax response', {
					length: response ? response.length : 0,
					preview: response ? response.substring(0, 200) : '',
				});
				if (handleGfPagingResponse($form, response, formId, pagingMeta, onPageChange)) {
					endLoading();
					scrollToWizard();
					return;
				}
				tryPagePost();
			})
			.fail(function (xhr) {
				wizardDebug('GF paging: wizard ajax failed', xhr.status, xhr.statusText);
				tryPagePost();
			});
	}

	function buildGfPagingPayload($form, formId, pagingMeta, mode) {
		mode = mode || 'ajax';
		syncGfPagingFields(
			$form,
			formId,
			pagingMeta.sourcePage || getEffectiveGfSourcePage($form, formId),
			pagingMeta.targetPage || 0
		);
		var data = serializeWizardForm($form, formId);
		var targetPage = pagingMeta.targetPage || 0;
		var sourcePage = pagingMeta.sourcePage || getEffectiveGfSourcePage($form, formId);

		data = data.filter(function (item) {
			return item.name !== 'action' && item.name !== 'nonce' && item.name !== 'gform_ajax';
		});

		upsertFormField(data, 'gform_target_page_number_' + formId, targetPage);
		upsertFormField(data, 'gform_source_page_number_' + formId, sourcePage);
		upsertFormField(data, 'is_submit_' + formId, '1');

		if (pagingMeta.direction === 'next') {
			upsertFormField(data, 'wc_gforms_next_page', targetPage);
			upsertFormField(data, 'wc_gforms_previous_page', '0');
		} else {
			upsertFormField(data, 'wc_gforms_previous_page', targetPage);
			upsertFormField(data, 'wc_gforms_next_page', '0');
		}

		var pagingFieldNames = [
			'gform_target_page_number_' + formId,
			'gform_source_page_number_' + formId,
			'is_submit_' + formId,
			'gform_submit',
			'gform_old_submit',
			'gform_ajax',
			'wc_gforms_next_page',
			'wc_gforms_previous_page',
		];

		if (mode === 'wizard') {
			data = data.filter(function (item) {
				return item.name !== 'gform_old_submit';
			});
			upsertFormField(data, 'action', 'tlth_book_wizard_gf_page');
			upsertFormField(data, 'nonce', config.nonce || '');
			upsertFormField(data, 'gform_ajax', 'form_id_' + formId);
			upsertFormField(data, 'gform_submit', formId);
		} else {
			data = data.filter(function (item) {
				return item.name !== 'gform_submit';
			});
			upsertFormField(data, 'gform_old_submit', formId);
		}

		wizardDebug('GF paging payload mode=' + mode, {
			targetPage: targetPage,
			sourcePage: sourcePage,
			fieldCount: data.length,
			hasState: data.some(function (item) {
				return item.name === 'state_' + formId;
			}),
			targetFields: data
				.filter(function (item) {
					return pagingFieldNames.indexOf(item.name) !== -1;
				})
				.map(function (item) {
					return item.name + '=' + item.value;
				}),
		});

		return $.param(data);
	}

	function parseGfResponseDocument(html) {
		if (!html || typeof html !== 'string') {
			return $('<div>');
		}

		try {
			// Do not use $.parseHTML(html, document, true) — it loads every <img> in the response.
			if (window.DOMParser) {
				var doc = new DOMParser().parseFromString(html, 'text/html');
				return $(doc.body);
			}
			var template = document.createElement('template');
			template.innerHTML = html;
			return $('<div>').append(template.content.cloneNode(true));
		} catch (err) {
			return $('<div>').html(html);
		}
	}

	function extractGformHtml($doc, formId) {
		var $wrap = $doc.find('#gform_wrapper_' + formId).first();
		if ($wrap.length) {
			return $wrap[0].outerHTML;
		}

		$wrap = $doc.find('.gform_variation_wrapper').first();
		if ($wrap.length && $wrap.find('#gform_wrapper_' + formId).length) {
			return $wrap[0].outerHTML;
		}

		$wrap = $doc.find('#gform_' + formId).first();
		if ($wrap.length) {
			var $parent = $wrap.closest('.gform_wrapper, .gform_variation_wrapper');
			return ($parent.length ? $parent : $wrap)[0].outerHTML;
		}

		$wrap = $doc.find('.gform_wrapper').first();
		if ($wrap.length) {
			return $wrap[0].outerHTML;
		}

		return '';
	}

	function getGfPageFromFragment(fragment, formId) {
		if (!fragment) {
			return 0;
		}

		var $frag = $('<div>').html(fragment);
		var sourceName = 'gform_source_page_number_' + formId;
		var maxSource = 0;

		$frag.find('#' + sourceName + ', input[name="' + sourceName + '"]').each(function () {
			var v = parseInt($(this).val(), 10) || 0;
			if (v > maxSource) {
				maxSource = v;
			}
		});

		if (maxSource) {
			return maxSource;
		}

		var visiblePage = 0;
		$frag.find('.gform_page').each(function () {
			var $page = $(this);
			if ($page.is(':hidden') || $page.css('display') === 'none') {
				return;
			}
			var match = ($page.attr('id') || '').match(/gform_page_\d+_(\d+)/);
			if (match) {
				visiblePage = parseInt(match[1], 10) || visiblePage;
			}
		});

		if (visiblePage) {
			return visiblePage;
		}

		var $page = $frag.find('.gform_page').first();
		if ($page.length && $page.attr('id')) {
			var pageMatch = $page.attr('id').match(/gform_page_\d+_(\d+)/);
			if (pageMatch) {
				return parseInt(pageMatch[1], 10) || 0;
			}
		}

		return 0;
	}

	function handleGfValidationFailure($form, formId, pagingMeta, parsed, html) {
		html = html || '';
		var failedPage =
			(parsed && (parsed.page_number || parsed.pageNumber)) ||
			getGfPageFromFragment(html, formId) ||
			(pagingMeta && pagingMeta.sourcePage) ||
			getEffectiveGfSourcePage($form, formId);

		if (html && html.indexOf('gform') !== -1) {
			replaceGformMarkup($form, html);
		}

		syncGfPagingFields($form, formId, failedPage, '0');
		refreshGfAfterMarkupChange($form, $form.closest('[data-book-wizard]'), formId, failedPage);
		return true;
	}

	function handleGfPagingResponse($form, response, formId, pagingMeta, onPageChange) {
		var normalized = normalizeGfAjaxResponse(typeof response === 'string' ? response : '');
		var parsed = normalized.parsed;
		var html = normalized.html || (typeof response === 'string' ? response : '');

		if (parsed) {
			wizardDebug('GF paging: parsed status', parsed.status);
		}

		if (parsed && parsed.status === 'validation_failed') {
			return handleGfValidationFailure($form, formId, pagingMeta, parsed, parsed.html || html);
		}

		if (parsed && (parsed.status === 'complete' || parsed.status === 'confirmation')) {
			wizardLog('GF paging: unexpected confirmation/complete on page change', parsed.status);
			return false;
		}

		var $doc = parseGfResponseDocument(html);
		var fragment = parsed && parsed.html ? html : extractGformHtml($doc, formId);

		if (isConfirmationForForm(fragment, formId)) {
			wizardLog('GF paging: confirmation for form ' + formId + ' in response');
			return false;
		}

		if (typeof fragment !== 'string' || fragment.indexOf('gform') === -1) {
			if (
				parsed &&
				(parsed.is_valid === false || parsed.isValid === false || parsed.status === 'invalid')
			) {
				return handleGfValidationFailure($form, formId, pagingMeta, parsed, html);
			}

			wizardLog('GF paging: no gform fragment found', {
				hasHtml: !!html,
				htmlLength: html ? html.length : 0,
				parsedStatus: parsed && parsed.status,
			});
			unlockGfSubmission($form, formId);
			if (pagingMeta && pagingMeta.sourcePage) {
				syncGfPagingFields($form, formId, pagingMeta.sourcePage, '0');
			}
			return false;
		}

		var responsePage = getGfPageFromFragment(fragment, formId);
		var expectedPage = pagingMeta && pagingMeta.targetPage ? pagingMeta.targetPage : 0;
		var currentPage = getEffectiveGfSourcePage($form, formId);
		var hasValidation =
			fragment.indexOf('gform_validation_error') !== -1 ||
			fragment.indexOf('gform_validation_errors') !== -1;

		wizardDebug('GF paging: pages', {
			currentPage: currentPage,
			expectedPage: expectedPage,
			responsePage: responsePage,
			hasValidation: hasValidation,
		});

		if (hasValidation) {
			replaceGformMarkup($form, fragment);
			refreshGfAfterMarkupChange(
				$form,
				$form.closest('[data-book-wizard]'),
				formId,
				pagingMeta.sourcePage || responsePage || currentPage
			);
			return true;
		}

		if (
			expectedPage &&
			responsePage &&
			responsePage !== expectedPage
		) {
			wizardLog('GF paging: server returned page ' + responsePage + ', expected ' + expectedPage);
			return false;
		}

		if (expectedPage && responsePage === expectedPage) {
			// OK
		} else if (responsePage && responsePage !== currentPage) {
			expectedPage = responsePage;
		}

		replaceGformMarkup($form, fragment);

		var resolvedPage = expectedPage || responsePage || getEffectiveGfSourcePage($form, formId);
		refreshGfAfterMarkupChange($form, $form.closest('[data-book-wizard]'), formId, resolvedPage);

		if (typeof onPageChange === 'function') {
			onPageChange(resolvedPage);
		}

		return true;
	}

	function replaceGformMarkup($form, html) {
		var $html = $('<div>').html(html);
		$html.find('[data-book-wizard-footer], .book-wizard-footer').remove();

		var $newWrapper = $html.find('.gform_variation_wrapper').first();
		var $existing = $form.find('.gform_variation_wrapper').first();

		var formId = getGfFormId($form);

		if ($newWrapper.length && $existing.length) {
			$existing.replaceWith($newWrapper);
			dedupeGfPagingFieldsAfterReplace($form, formId);
			return;
		}

		$newWrapper = $html
			.filter('.gform_wrapper, .gform_variation_wrapper')
			.add($html.find('.gform_wrapper, .gform_variation_wrapper'))
			.first();
		$existing = $form.find('.gform_wrapper, .gform_variation_wrapper').first();

		if ($newWrapper.length && $existing.length) {
			$existing.replaceWith($newWrapper);
			dedupeGfPagingFieldsAfterReplace($form, formId);
			return;
		}

		if ($existing.length) {
			$existing.html($html.html());
		}

		dedupeGfPagingFieldsAfterReplace($form, formId);
	}

	function dedupeGfPagingFieldsAfterReplace($form, formId) {
		if (!formId) {
			formId = getGfFormId($form);
		}
		var page = getEffectiveGfSourcePage($form, formId);
		syncGfPagingFields($form, formId, page, '0');
	}

	function setGfPageFields($form, formId, sourcePage, targetPage, direction) {
		syncGfPagingFields($form, formId, sourcePage, targetPage);

		if (direction === 'next') {
			$form.find('input[name="wc_gforms_next_page"]').val(targetPage);
			$form.find('input[name="wc_gforms_previous_page"]').val('0');
		} else {
			$form.find('input[name="wc_gforms_previous_page"]').val(targetPage);
			$form.find('input[name="wc_gforms_next_page"]').val('0');
		}
	}

	function getGfFormElement($form, formId) {
		var el = document.getElementById('gform_' + formId);
		if (el) {
			return el;
		}
		if ($form && $form.length && $form.attr('id') === 'gform_' + formId) {
			return $form[0];
		}
		return $form && $form.length ? $form[0] : null;
	}

	function tryGfNativePageChange($form, formId, direction, sourcePage, targetPage) {
		var submission = window.gform && window.gform.submission;
		if (!submission || typeof submission.submitForm !== 'function') {
			wizardLog('Native GF paging unavailable (gform.submission.submitForm missing)');
			return false;
		}

		var formEl = getGfFormElement($form, formId);
		if (!formEl) {
			wizardLog('Native GF paging: form element #gform_' + formId + ' not found');
			return false;
		}

		setGfPageFields($form, formId, sourcePage, targetPage, direction);

		var type =
			direction === 'next' ? submission.SUBMISSION_TYPE_NEXT : submission.SUBMISSION_TYPE_PREVIOUS;
		var method = submission.getSubmissionMethod(formEl);

		if (method === submission.SUBMISSION_METHOD_POSTBACK || method === 'postback') {
			wizardLog('Native GF paging skipped: form submission method is postback (needs ajax)');
			return false;
		}

		wizardLog('Native GF paging via gform.submission.submitForm', {
			formId: formId,
			direction: direction,
			method: method,
			elementId: formEl.id,
			sourcePage: sourcePage,
			targetPage: targetPage,
		});

		if (!submission.lockSubmission(formEl)) {
			wizardLog('Native GF paging: submission locked — using AJAX fallback');
			return false;
		}

		var $card = $form.closest('.book-wizard-card');
		$form.addClass('book-wizard-loading');
		$card.addClass('book-wizard-loading');

		submission
			.submitForm(formEl, type, method)
			.then(function () {
				if ($form.find('.gform_validation_error, .gform_validation_errors').length) {
					wizardLog('Native GF paging: validation errors on page', sourcePage);
					syncGfPagingFields($form, formId, sourcePage, '0');
					refreshGfAfterMarkupChange($form, $form.closest('[data-book-wizard]'), formId, sourcePage);
					return;
				}
				var page = targetPage;
				wizardLog('Native GF paging success, current page:', page);
				$form.data('tlthWizardGfPage', page);
				syncGfPagingFields($form, formId, page, '0');
				$form.trigger('tlthGfPageChanged', [page]);
			})
			.catch(function (err) {
				wizardLog('Native GF paging failed', err);
				unlockGfSubmission($form, formId);
			})
			.finally(function () {
				submission.unlockSubmission(formEl);
				$form.removeClass('book-wizard-loading');
				$card.removeClass('book-wizard-loading');
			});

		return true;
	}

	function navigateGfPage($form, direction, formId) {
		if (!formId) {
			return;
		}

		var maxPage = parseInt(config.gfPageCount, 10) || 1;
		var sourcePage = getEffectiveGfSourcePage($form, formId);
		var targetPage = direction === 'next' ? sourcePage + 1 : Math.max(1, sourcePage - 1);

		syncGfPagingFields($form, formId, sourcePage, targetPage);

		wizardLog('navigateGfPage', {
			direction: direction,
			formId: formId,
			sourcePage: sourcePage,
			targetPage: targetPage,
			maxPage: maxPage,
		});

		if (direction === 'next' && !validateGfCurrentPage(formId)) {
			wizardLog('GF paging blocked: client-side validation failed on page', sourcePage);
			return;
		}

		if (direction === 'next' && sourcePage >= maxPage) {
			wizardLog('GF paging blocked: already on last page');
			return;
		}

		if (targetPage > maxPage) {
			wizardLog('GF paging blocked: target exceeds max page');
			return;
		}

		if (tryGfNativePageChange($form, formId, direction, sourcePage, targetPage)) {
			return;
		}

		wizardLog('Using custom AJAX paging fallback');
		setGfPageFields($form, formId, sourcePage, targetPage, direction);
		unlockGfSubmission($form, formId);

		$form.data('tlthGfPagingMeta', {
			sourcePage: sourcePage,
			targetPage: targetPage,
			direction: direction,
		});
		$form.data('tlthGfPaging', true);
		$form.trigger('submit');
	}

	function scrollToWizard() {
		var el = document.getElementById('personnaliser-le-livre');
		if (el) {
			el.scrollIntoView({ behavior: 'smooth', block: 'start' });
		}
	}

	function bootWizard() {
		initBookPersonalizationWizard();

		// WC GF plugin initializes after DOM ready.
		setTimeout(initBookPersonalizationWizard, 100);
		setTimeout(initBookPersonalizationWizard, 600);
	}

	function checkExistingCartNotice() {
		var $root = $('[data-book-wizard]');
		if (!$root.length || $root.hasClass('is-added-to-cart')) {
			return;
		}

		var $notice = $('.woocommerce-notices-wrapper .woocommerce-message').first();
		if ($notice.length && isWooCommerceCartNoticeHtml($notice.text())) {
			var $form = $('[data-book-wizard] form.cart, [data-book-wizard] form.variations_form').first();
			showCartConfirmation($root, $form);
		}
	}

	$(document).ready(function () {
		patchGfApplyRulesSafe();
		bootWizard();
		checkExistingCartNotice();

		$(document.body).on('added_to_cart.tlthBookWizard', function () {
			var $root = $('[data-book-wizard]');
			if ($root.length && $root.hasClass('is-added-to-cart')) {
				$(document.body).trigger('wc_fragment_refresh');
			}
		});

		document.addEventListener('gform/ajax/post_page_change', function (event) {
			var detail = event.detail || {};
			wizardLog('gform/ajax/post_page_change', detail);

			var $form = $('[data-book-wizard] form.cart, [data-book-wizard] form.variations_form').first();
			if (!$form.length || parseInt(detail.formId, 10) !== getGfFormId($form)) {
				return;
			}

			if ($form.data('tlthAddToCartInProgress')) {
				return;
			}

			overrideWcGfPagingHandlers($form);
			hideGfProgress($form.find('.gform_wrapper').first());
			$form.trigger('tlthGfPageChanged', [detail.pageNumber || getGfCurrentPage($form, detail.formId)]);
		});
	});
	$(document).on('gform_post_render', function (event, formId) {
		var $form = $('[data-book-wizard] form.cart, [data-book-wizard] form.variations_form').first();
		if (!$form.length || parseInt(formId, 10) !== getGfFormId($form)) {
			return;
		}
		if ($form.data('wizardInitialized')) {
			var $root = $('[data-book-wizard]');
			var formIdNum = parseInt(formId, 10);
			overrideWcGfPagingHandlers($form);
			hideGfProgress($form.find('.gform_wrapper').first());

			if ($root.hasClass('is-last-step')) {
				var lastPage = getLastGfPage($form, formIdNum);
				setupLastGfPage($form, $root, formIdNum, true);
				showGfPageInWizard($form, formIdNum, lastPage);
				syncGfPagingFields($form, formIdNum, lastPage, '0');
				return;
			}

			var gfPage = getGfCurrentPage($form, formIdNum);
			var variationCount = $form.find('table.variations tbody tr').length;
			var gfPageCount = getLastGfPage($form, formIdNum);
			var isLast = variationCount + gfPage === variationCount + gfPageCount;
			setupLastGfPage($form, $root, formIdNum, isLast);
			return;
		}
		initBookPersonalizationWizard();
	});
})(jQuery);
