/**
 * Base for all containers used to display just one of their children at a time.
 *
 * Method _switchContent() is used to change which child is currently selected
 * to be displayed.
 */
asm.ui.ContentSwitcher = asm.ui.Container.extend({
	/**
	 * @copydoc Container::Container()
	 *
	 * Additional configuration options:
	 * @arg @a current namem of currently selected child
	 */
	constructor: function (config) {
		var defaults = {
			current: ''
		};
		this.base($.extend(defaults, config));

		for (var i in this.config.children) {
			if (i != '') {
				this.config.children[i].bind('panel.adjustRequest', (function closure () {
					var childName = i;
					return function (params) {
						params.params.unshift(childName);
					};
				})());
			}
		}
	},
	/**
	 * Calls selected method on currently selected child.
	 * @tparam string fnName method name
	 * @tparam mixed [...] method arguments
	 */
	_callOnCurrentChild: function (fnName) {
		if (this.config.children[this.config.current] != undefined) {
			var args = Array.prototype.slice.call(arguments, 1);
			this.config.children[this.config.current][fnName]
				.apply(this.config.children[this.config.current], args);
		}
	},
	/**
	 * Selects likely name of next selected child based on first display parameter.
	 * @treturn string either child name (if child named as first display parameter
	 *		exists), or empty string
	 */
	_selectNextChild: function () {
		if (this.config.children[this._params[0]] != undefined) {
			return this._params[0];
		}
		return '';
	},
	/**
	 * Selects a child to be displayed.
	 * If selected child is currently displayed, it is hidden, and selected one
	 * is shown. Displayed child is passed this instance's display parameters
	 * (apart from first one if @a childName is not an empty string)
	 * @tparam string childName name of child to be displayed
	 */
	_switchContent: function (childName) {
		var params = this._params.slice((childName == '') ? 0 : 1);
        // This is hack existing because of the AppMainPanel
        // It works this way: When the first parameter is an empty string, it is left in params, but if it's not empty, it is removed.
        // This way, AppMainPanel gets the entire hashstring, while the registration form and other forms before login only get the part behind the form name.

		if (childName != this.config.current) {
			this._callOnCurrentChild('hide');
			this.config.current = childName;
		}
		this._callOnCurrentChild('show', params);
	},
	/**
	 * <b>Doesn't do a damn thing</b> (selected-child-display logic is contained
	 * in _adjustContent() method.
	 */
	_showContent: function () {
	},
	/**
	 * @copybrief Container::_adjustContent()
	 * 
	 * Selects child to be displayed based on first display parameter. If a child
	 * with that name exists, it is shown and adjusted with all of this instance's
	 * display parameters apart from first one. If it doesn't exist and child with
	 * empty string for name exists, then that child is shown and passed @b all
	 * of this instance's display parameters.
	 */
	_adjustContent: function () {
		this._switchContent(this._selectNextChild());
	},
	_hideContent: function () {
		this._callOnCurrentChild('hide');
	}
});
