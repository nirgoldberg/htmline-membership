/**
 * HTMLine Membership Front JS functions
 *
 * @author		Nir Goldberg
 * @package		js
 * @version		1.0.0
 */
var $ = jQuery,
	hmembershipFront = (function() {

		var self = {};

		/**
		 * params
		 */
		var params = {

			rtl:		$('html').attr('dir') && 'rtl' == $('html').attr('dir'),

		};

		/**
		 * init
		 *
		 * @since		1.0.0
		 * @param		N/A
		 * @return		N/A
		 */
		self.init = function() {

			// registration form
			registrationForm();

		};

		/**
		 * registrationForm
		 *
		 * Handles registration form
		 *
		 * @since		1.0.0
		 * @param		N/A
		 * @return		N/A
		 */
		var registrationForm = function() {

			// vars
			var form = $('.hmembership-form');

			if (!form.length)
				return;

			// form submission
			form.find('.hmembership-form-button').click(function() {
				if (!$(this).parent().hasClass('active')) {
					rfSubmission($(this));
				}
			});

		};

		/**
		 * rfSubmission
		 *
		 * Collects form data and sends to form api
		 *
		 * @since		1.0.0
		 * @param		el (jQuery)
		 * @return		N/A
		 */
		var rfSubmission = function(el) {

			// vars
			var form = el.closest('.hmembership-form'),
				nonce = form.children('#_wpnonce').val(),
				fields = form.children('table').find('tr'),
				fieldsArr = rfFields(fields),
				result = form.children('.result'),
				msg = '';

			// show loader
			el.parent().addClass('active');

			// hide result
			result.text('');

			// send data to form api
			$.ajax({
				type: 'post',
				dataType: 'json',
				url: _hmembership_front.ajaxurl,
				data: {
					action: 'hmembership_form_submission',
					nonce: nonce,
					fields: fieldsArr,
				},
				success: function(response, textStatus, jqXHR) {
					if (response.errors.length) {
						$.each(response.errors, function(i, error) {
							msg += _hmembership_front.strings.error + ' #' + error.code + ': ' + error.description + '<br />';
						});
					}

					if (response.data.length) {
						msg += _hmembership_front.strings.success + ' <b><i>' + response.data + '</i></b>';
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					msg += _hmembership_front.strings.failed + '<br />' + errorThrown;
				},
				complete: function(jqXHR, textStatus) {
					// hide loader
					el.parent().removeClass('active');

					// show result
					result.html(msg);
				}
			});

		};

		/**
		 * rfFields
		 *
		 * Collects form data
		 *
		 * @since		1.0.0
		 * @param		fields (array) Array of 'tr' jQuery elements
		 * @return		(object)
		 */
		var rfFields = function(fields) {

			// vars
			var fieldsArr = {};

			// build fieldsArr
			$.each(fields, function(i, field) {

				// vars
				var required = $(field).hasClass('required'),
					label_col = $(field).children('th'),
					input_col = $(field).children('td'),
					id = label_col.children('label').attr('for'),
					label = label_col.children('label').text(),
					input = input_col.children(),
					input_tag = input.prop('tagName'),
					type = '',
					value = '';

				switch (input_tag) {

					case 'INPUT':

						type = input.attr('type');
						value = input.val();
						break;

					case 'TEXTAREA':

						type = 'textarea';
						value = $.trim(input.val());
						break;

					case 'SELECT':

						type = 'select';
						value = input.val();
						break;

					case 'FIELDSET':

						var children = input.children(),
							inputs = children.find('input');

						type = children.first().find('input').attr('type');
						value = {};

						$.each(inputs, function() {
							if ($(this).prop('checked')) {
								value[$(this).val()] = $(this).next().text();
							}
						});
						break;

				}

				fieldsArr[id] = {
					type: type,
					label: label,
					value: value,
					required: required
				};

			});

			// return
			return fieldsArr;

		};

		// return
		return self;

	}

());

hmembershipFront.init();