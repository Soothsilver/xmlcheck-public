/**
 * Makes element stateful (with default, focus, and active states).
 *
 * Widget is always in one of four states:
 * @li @em default
 * @li @em hover - user has mouse over element
 * @li @em active - user clicked on the element
 * @li @em disabled - element is disabled (cannot change state to hover or active)
 *
 * Customizable class is applied to element indicating its current state (element
 * may be styled to reflect these states). State class consists of prefix
 * (@ref stateClassPrefix) and main class name (@ref defaultClass, @ref hoverClass,
 * @ref activeClass, @ref disabledClass).
 */
$.widget('ui.stateful', {
	options: {
		/** @type string
		 * prefix added to state classes (see class description)
		 */
		stateClassPrefix: 'ui-state-',
		/** @type string
		 * default state class (see class description)
		 */
		defaultClass: 'default',
		/** @type string
		 * hover state class (see class description)
		 */
		hoverClass: 'hover',
		/** @type string
		 * active state class (see class description)
		 */
		activeClass: 'active',
		/** @type string
		 * disabled state class (see class description)
		 */
		disabledClass: 'disabled',
		/** @type string
		 * Set to @c 'bound' to turn on group behavior.
		 * With group behavior on, only one element in the @ref group may be active
		 * at any time. Before element is activated, all other elements in the group
		 * are therefore deactivated.
		 */
		groupBehaviour: 'single',
		/** @type jQuerySel
		 * group for group behavior @see groupBehaviour
		 */
		group: $(),
		/** @type function
		 * Callback called whenever element is about to be activated.
		 * Widget behaviour in relation to this callback depends on @ref lateActivation.
		 * If @ref lateActivation is set to false (default), callback is called
		 * and widget is activated only if callback doesn't return false. Otherwise
		 * it is passed delegate for widget activation as second argument and must
		 * call it if the widget is to be activated. First argument passed to callback
		 * is the widget element in both cases.
		 */
		activate: null,
		/** @type function
		 * Callback called whenever element about to be deactivated.
		 * Behaves exactly like @ref activate.
		 */
		deactivate: null,
		/** @type bool
		 * set to @c true to turn on late activation (see @ref activate)
		 */
		lateActivation: false,
		/** @type bool
		 * set to @c true to turn on late deactivation (see @ref deactivate)
		 */
		lateDeactivation: false,
		/** @type bool
		 * set to @c true to enable click event propagation outside widget
		 */
		keepDefaultAction: false
	},
	_init: function () {
		this.option('state', 'default');
		this.options.group = this.options.group.not(this.element.get());
		this.element
			.css('cursor', 'pointer')
			.bind('mouseenter focus', $.proxy(this._focus, this))
			.bind('mouseleave blur', $.proxy(this._blur, this))
			.bind('click', $.proxy(this._toggle, this))
			.bind('keydown', $.proxy(function (event) {
				if (event.which == $.ui.keyCode.SPACE) {	// spacebar pressed
					this._toggle(event);
				}
			}, this));
	},
	_setOption: function (key, value) {
		var previousValue = this.options[key];
		if (value == previousValue) {
			return;
		}
		var self = this;
		var doChange = function () {
			$.Widget.prototype._setOption.call(self, key, value);
			if (key == 'state') {
				if (self.options.groupBehaviour == 'bound') {
					self.options.group.stateful('option', 'state', value);
				}
				if ((value == 'active') && (self.options.groupBehaviour == 'single')) {
					self.options.group.stateful('deactivate');
				}
				self._changeSkin(previousValue);
			}
		};
		var act = function (userCallback, lateStateChange) {
			if ($.isFunction(userCallback)) {
				if (lateStateChange) {
					userCallback.call(self.element, doChange);
				} else {
					if (userCallback.call(self.element) !== false) {
						doChange();
					}
				}
			} else {
				doChange();
			}
		};
		switch (key) {
			case 'state':
				if ($.inArray(value, ['default', 'hover', 'active']) == -1) {
					value = 'default';
				}
				switch (value) {
					case 'active':
						if (!this.options.disabled) {
							act(this.options.activate, this.options.lateActivation);
						}
						break;
					case 'default':
						act(this.options.deactivate, this.options.lateDeactivation);
						break;
					default:
						if (!this.options.disabled) {
							doChange();
						}
						break;
				}
				break;
			case 'groupBehaviour':
				this.options.group.stateful('option', 'groupBehaviour', value);
				break;
			default:
				doChange();
				break;
		}
	},
	/**
	 * Switches element state class to reflect current state.
	 * @note Also triggers 'activate' or 'deactivate' event if appropriate.
	 * @tparam string previousValue previous state
	 */
	_changeSkin: function (previousValue) {
		this._stripSkin();
		this.element.addClass(this.options.stateClassPrefix + this.options[this.options.state + 'Class']);
		this._trigger('change', null, this.options.state);
		if (this.options.state == 'active') {
			this._trigger('activate');
		} else if (previousValue == 'active') {
			this._trigger('deactivate');
		}
	},
	/**
	 * Strips element state class.
	 */
	_stripSkin: function () {
		var elem = this.element,
			options = this.options;
		$.each(['default', 'hover', 'active'], function (i, cls) {
			elem.removeClass(options.stateClassPrefix + options[cls + 'Class']);
		});
	},
	/**
	 * Changes state to @c 'hover' if it isn't currently @c 'active'.
	 */
	_focus: function () {
		if (this.options.state != 'active') {
			this.option.call(this, 'state', 'hover');
		}
	},
	/**
	 * Changes state to @c 'default' if it is currently @c 'hover'.
	 */
	_blur: function () {
		if (this.options.state == 'hover') {
			this.option.call(this, 'state', 'default');
		}
	},
	/**
	 * Toggles between 'default' and 'active' states as a reaction to click/keydown event.
	 * @tparam event event
	 */
	_toggle: function (event) {
		if (!this.options.keepDefaultAction) {
			event.preventDefault();
		}
		this.toggle.call(this);
	},
	/**
	 * Changes state to @c 'active'.
	 */
	activate: function () {
		this.toggle.call(this, true);
	},
	/**
	 * Changes state to @c 'default'.
	 */
	deactivate: function () {
		this.toggle.call(this, false);
	},
	/**
	 * Toggles between 'default' and 'active' states programatically.
	 * @tparam bool activate @optional (if not specified, state will be changed)
	 */
	toggle: function (activate) {
		if (activate == undefined) {
			this.toggle.call(this, (this.options.state != 'active'));
		} else {
			this.option('state', (activate ? 'active' : 'default'));
		}
	}
});