/**
 * HTMLine Membership JS functions
 *
 * @author		Nir Goldberg
 * @package		js
 * @version		1.0.0
 */
var $ = jQuery,
	hmembership = (function() {

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

			// dynamic settings
			dynamicSettings();

		};

		/**
		 * dynamicSettings
		 *
		 * Handles dynamic setting sections
		 *
		 * @since		1.0.0
		 * @param		N/A
		 * @return		N/A
		 */
		var dynamicSettings = function() {

			// vars
			var sections_wrap = $('.hmembership-admin-box-content-sortable');

			if (!sections_wrap.length)
				return;

			// sortable sections
			sections_wrap.sortable({
				start:	dsSortableStart,
				stop:	dsSortableStop,
				update:	dsSortableUpdate
			}).disableSelection();

			// add section
			$('.add-section').click(function() {
				dsAddSection($(this));
			});

			// remove section
			sections_wrap.on('click', '.remove-section', function() {
				dsRemoveSection($(this));
			});

		};

		/**
		 * dsSortableStart
		 *
		 * Stores radio 'checked' property
		 *
		 * @since		1.0.0
		 * @param		event (Event)
		 * @param		ui (Object)
		 * @return		N/A
		 */
		var dsSortableStart = function(event, ui) {

			// vars
			var el = $(ui.item),
				sections = el.parent().children();

			sections.find('input[type="radio"]').each(function () {
				if ($(this).prop('checked')) {
					$(this).attr('data-checked', true);
				} else {
					$(this).attr('data-checked', false);
				}
			});

		};

		/**
		 * dsSortableStop
		 *
		 * Sets radio 'checked' property
		 *
		 * @since		1.0.0
		 * @param		event (Event)
		 * @param		ui (Object)
		 * @return		N/A
		 */
		var dsSortableStop = function(event, ui) {

			// vars
			var el = $(ui.item),
				sections = el.parent().children();

			sections.find('input[type="radio"]').each(function () {
				if ($(this).attr('data-checked') == 'true') {
					$(this).prop('checked', true);
				} else {
					$(this).prop('checked', false);
				}
			});

		};

		/**
		 * dsSortableUpdate
		 *
		 * Sorts sections
		 *
		 * @since		1.0.0
		 * @param		event (Event)
		 * @param		ui (Object)
		 * @return		N/A
		 */
		var dsSortableUpdate = function(event, ui) {

			// vars
			var el = $(ui.item),
				sections = el.parent().children();

			// sort section fields IDs
			sections.each(function(index, section) {
				dsSortSectionFields($(section), index+1);
			});

		};

		/**
		 * dsAddSection
		 *
		 * Adds section
		 *
		 * @since		1.0.0
		 * @param		el (jQuery) Add Section button
		 * @return		N/A
		 */
		var dsAddSection = function(el) {

			// vars
			var sections_wrap = el.prev(),
				sections = sections_wrap.children(),
				newSection = $('.hmembership-dynamic-section-template').children().clone(true);

			// reset newSection fields
			dsResetSection(newSection);

			if (!sections_wrap.parent().hasClass('no-sections')) {
				// sort section fields IDs
				dsSortSectionFields(newSection, sections.length+1);
			} else {
				// remove no sections indication
				sections_wrap.parent().removeClass('no-sections');
			}

			// expose section
			newSection.appendTo(sections_wrap);

		};

		/**
		 * dsResetSection
		 *
		 * Resets section fields
		 *
		 * @since		1.0.0
		 * @param		el (jQuery)
		 * @return		N/A
		 */
		var dsResetSection = function(el) {

			// text/password/number/email/textarea
			el.find('input[type="text"],input[type="password"],input[type="number"],textarea').val('');

			// select/multiselect
			el.find('select').children().removeAttr('selected');

			// radio/checkbox
			el.find('input[type="radio"],input[type="checkbox"]').removeAttr('checked');

		}

		/**
		 * dsSortSectionFields
		 *
		 * Sorts section fields IDs
		 *
		 * @since		1.0.0
		 * @param		el (jQuery)
		 * @param		index (int) current section index
		 * @return		N/A
		 */
		var dsSortSectionFields = function(el, index) {

			// text/password/number/email/textarea/select/multiselect
			el.find('input[type="text"],input[type="password"],input[type="number"],textarea,select').each(function() {
				// vars
				var id = $(this).attr('id');

				id = id.substring(0, id.lastIndexOf('_'));

				// modify id
				$(this).attr('id', id + '_' + index);

				// modify label
				$(this).closest('tr').find('label').attr('for', id + '_' + index);
			});

			// radio/checkbox
			el.find('input[type="radio"],input[type="checkbox"]').each(function() {
				// vars
				var id = $(this).attr('id');

				suffixPos = id.lastIndexOf('_');
				suffix = id.substring(suffixPos);
				prefix = id.substring(0, id.lastIndexOf('_'));
				prefix = prefix.substring(0, prefix.lastIndexOf('_'));

				// modify id
				$(this).attr('id', prefix + '_' + index + suffix);

				// modify direct label
				$(this).parent('label').attr('for', prefix + '_' + index + suffix);

				// modify fieldgroup label
				$(this).closest('tr').children('th').children('label').attr('for', prefix + '_' + index);
			});

			// select/multiselect/radio/checkbox/hidden
			el.find('select,input[type="radio"],input[type="checkbox"],input[type="hidden"]').each(function() {
				// vars
				var name = $(this).attr('name');

				suffixPos = name.lastIndexOf('[]');
				suffix = name.substring(suffixPos);
				prefix = name.substring(0, name.lastIndexOf('['));
				prefix = prefix.substring(0, prefix.lastIndexOf('['));

				// modify name
				$(this).attr('name', prefix + '[' + (index-1) + ']' + suffix);
			});

		}

		/**
		 * dsRemoveSection
		 *
		 * Removes section
		 *
		 * @since		1.0.0
		 * @param		el (jQuery) Remove Section button
		 * @return		N/A
		 */
		var dsRemoveSection = function(el) {

			// vars
			var currentSection = el.parent(),
				sections_wrap = currentSection.parent(),
				sections = sections_wrap.children();

			// remove current section
			currentSection.remove();

			// sort section fields IDs
			sections = sections_wrap.children();

			sections.each(function(index, section) {
				dsSortSectionFields($(section), index+1);
			});

			// maybe no sections indication
			if (!sections.length) {
				sections_wrap.parent().addClass('no-sections');
			}

		};

		// return
		return self;

	}

());

hmembership.init();