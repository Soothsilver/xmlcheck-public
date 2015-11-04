/**
 * Provides means for application navigation through window location hash property.
 * Can be used to create JavaScript applications with transparent locations and
 * working "Back" and "Forward" browser buttons.
 */
asm.ui.Navigator = Base.extend({
	/**
	 * Initializes instance using supplied configuration.
	 * @tparam object config configuration properties
	 *
	 * No configuration properties are supported at this time.
	 */
	constructor: function (config) {
		var defaults = {};
		this.config = $.extend(defaults, config);

		this.confirmed = '';
		this.hash = null;
	},
	/**
	 * Starts navigation management.
	 * @note Must be called before using any other methods.
	 * @note Should be called only after the application is fully loaded.
	 */
	init: function () {
		var self = this;
		setInterval(function () {
			self.refresh.call(self);
		}, 10);
	},
	/**
	 * Checks whether window location hash has changed and calls hashChange() if
	 * it has.
	 */
	refresh: function () {
		if (window.location.hash !== this.hash) {
			this.hash = window.location.hash;
			this.hashChange();
		}
	},
	/**
	 * Triggers @c hashchange event.
	 * Event is triggered with following properties:
	 * @li @c stack (array) hash parts (window location hash split by hash character)
	 */
	hashChange: function () {
		var stack = this.hash.replace('%23', '#').split('#');	// Safari h4ck
		stack.shift();
		this.trigger('hashchange', {
			stack: stack
		});
	},
	/**
	 * Changes window location hash based on supplied arguments.
	 * @tparam string [...] hash parts (will be joined with hash character to form
	 *		new value of window location)
	 * @treturn bool false
	 */
	redirect: function () {
		window.location.hash = $.makeArray(arguments).join('#');
		return false;
	},
	/**
	 * Redirects to last confirmed valid location.
	 * @see confirm()
	 */
	cancel: function () {
		window.location.hash = this.confirmed;
	},
	/**
	 * Confirms current location as valid.
	 * @see cancel()
	 */
	confirm: function () {
		this.confirmed = this.hash;
	}
});
asm.ui.Navigator.implement(asm.ui.Eventful);