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

			rtl:	$('html').attr('dir') && 'rtl' == $('html').attr('dir'),

		};

		/**
		 * init
		 *
		 * @since		1.0.0
		 * @param		N/A
		 * @return		N/A
		 */
		self.init = function() {



		};

		// return
		return self;

	}

());

hmembership.init();