/**
 * Manages error display and storage.
 * @note It doesn't matter what type supplied errors are, only their string
 *		representation is used.
 */
asm.ui.ErrorManager = Base.extend({
	/**
	 * Initializes instance with supplied configuration.
	 */
	constructor: function (config) {
		var defaults = {};
		this.config = $.extend(defaults, config);

		this._current = {};
		this._log = [];
	},
	/**
	 * Shows supplied error.
	 * @abstract
	 * @tparam mixed error
	 */
	_show: function (error) {
	},
	/**
	 * Hides supplied error.
	 * @tparam mixed error
	 */
	_hide: function (error) {
		this._removeFromCurrent(error);
	},
	/**
	 * Adds error to set of displayed errors if its message isn't shown already.
	 * @tparam mixed error
	 * @tparam bool true if error was added successfully, false if its message
	 *		is already being shown.
	 */
	_addToCurrent: function (error) {
		var message = error.toString();
		if (this._current[message] == undefined) {
			this._current[message] = error;
			return true;
		}
		return false;
	},
	/**
	 * Removes error from set of currently displayed errors.
	 * @tparam mixed error
	 */
	_removeFromCurrent: function (error) {
		delete this._current[error.toString()];
	},
	/**
	 * Adds new error to this error manager (to be displayed and stored).
	 * @tparam mixed error
	 */
	add: function (error) {
		this._log.unshift(error);
		if (this._addToCurrent(error)) {
			this._show(error);
		}
	},
	/**
	 * Clears all displayed errors.
	 */
	clearDisplay: function () {
		$.each(this._current, $.proxy(function (msg) {
			this._hide(msg);
		}, this));
	},
	/**
	 * Gets errors stored by this manager.
	 * @treturn array
	 */
	getLog: function () {
		return this._log.slice(0);
	},
	/**
	 * Removes stored errors from this manager.
	 */
	clearLog: function () {
		this._log = [];
	},
	/**
	 * Clears all errors from this manager (both from display and from log).
	 */
	clear: function () {
		this.clearDisplay();
		this.clearLog();
	}
});