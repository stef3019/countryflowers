/**
 * radio field script
 *
 * @package field
 * @version 1.0
 * @author  g5plus
 */

/**
 * Define class field
 */
var GSF_SwitchClass = function($container) {
	this.$container = $container;
};

(function($) {
	"use strict";

	/**
	 * Define class field prototype
	 */
	GSF_SwitchClass.prototype = {
		init: function() {
			var $check = this.$container.find('[type="checkbox"]'),
				$field = this.$container.find('[data-field-control]');
			$check.on('change', function () {
				$field.val($check.prop('checked') ? 'on' : '');
				$field.trigger('change');
			});
		},
		getValue: function() {
			var $check = this.$container.find('[data-field-control]');
			return $check.val();
		}
	};

})(jQuery);