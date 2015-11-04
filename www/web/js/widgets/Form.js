/**
 * Enhances form to manage contained @ref fieldset widgets.
 * @note Works only with @ref fieldset "fieldsets", cannot be used on
 *		@ref field "fields" directly.
 */
$.widget('ui.form', {
	options: {
		/** @type bool
		 * passed as @ref fieldset::blend "blend" option to contained fieldsets
		 */
		blend: false,
		/** @type bool
		 * passed as @ref fieldset::simple "simple" option to contained fieldsets
		 */
		simple: false,
		/** @type bool
		 * set to true to hide submit button and disallow form submit (use on
		 * forms that don't interact with server)
		 */
		offline: false,
		/** @type object
		 * Additional buttons. Keys are not used, values must be objects with
		 * following properties:
		 * @li @c action function called on button click
		 * @li @c icon @optional button icon
		 * @li @c label button label
		 */
		buttons: {},
		/** @type mixed
		 * submit button icon (null to show no icon)
		 */
		submitIcon: 'ui-icon-check',
		/** @type string
		 * submit button text
		 */
		submitText: asm.lang.general.submit,
		/** @type mixed
		 * Form submit implementation (function), or null to send submit form by
		 * conventional means (using POST request). Web applications with
		 * JavaScript-based GUI should not use the default method, as it forces
		 * page refresh.
		 *
		 */
		submit: null
	},
	_create: function () {
		this.element.addClass('ui-form');
		this.sets = this.element.find('fieldset')
			.fieldset();
		this.actionBar = $('<div></div>')
			.addClass('ui-form-actions')
			.addClass('ui-helper-clearfix')
			.appendTo(this.element);
		this.submit = $('<button type="submit"></button>')
			.button()
			.addClass('ui-priority-primary')
			.appendTo(this.actionBar);
		this.buttons = $();
		this._setBlend();
		this._setSimple();
		this._setOffline();
		this._setButtons();
		this._setSubmitText();
		this._setSubmitIcon();

		this.index = {};
		this.incorrect = 0;
		this.sets.bind('fieldsetincorrect', $.proxy(this._lock, this))
			.bind('fieldsetcorrect', $.proxy(this._unlock, this));
		this.element.bind('submit', $.proxy(this._submit, this));
	},
	_setOption: function (key, value) {
		$.Widget.prototype._setOption.call(this, key, value);
		switch (key) {
			case 'blend':
				this._setBlend();
				break;
			case 'simple':
				this._setSimple();
				break;
			case 'offline':
				this._setOffline();
				break;
			case 'buttons':
				this._setButtons();
				break;
			case 'submitText':
				this._setSubmitText();
				break;
			case 'submitIcon':
				this._setSubmitIcon();
				break;
		}
	},
	_setOffline: function () {
		this.submit.toggle(!this.options.offline);
	},
	_setBlend: function () {
		this.sets.fieldset('option', 'blend', this.options.blend);
	},
	_setSimple: function () {
		this.sets.fieldset('option', 'simple', this.options.simple);
	},
	_setButtons: function () {
		this.buttons.remove();
		this.buttons = $();
		var self = this;
		$.each(this.options.buttons, function (name, config) {
			var button = $('<button type="button"></button>')
				.button({
					text: !!config.label,
					icons: {
						primary: config.icon || null,
						secondary: null
					},
					label: config.label
				})
				.addClass('ui-priority-secondary')
				.appendTo(self.actionBar)
				.bind('click', config.action);
			self.buttons = self.buttons.add(button);
		});
	},
	_setSubmitText: function () {
		this.submit.button('option', 'label', this.options.submitText);
	},
	_setSubmitIcon: function () {
		this.submit.button('option', 'icons', {
			primary: this.options.submitIcon || null,
			secondary: null
		});
	},
	/**
	 * Increases internal count of fieldsets in error state by 1 and locks form.
	 * Form cannot be submitted while locked.
	 * @note Triggers @c lock event if no fieldsets were in error state.
	 * @see _unlock()
	 */
	_lock: function () {
		if (!(this.incorrect++)) {
			this._trigger('lock');
			this.submit.button('disable');
		}
	},
	/**
	 * Decreases internal count of fieldsets in error state by 1 and unlocks form
	 * if no fieldsets are in error state any more.
	 * @note Triggers @c unlock event in case it unlocks form.
	 * @see _lock()
	 */
	_unlock: function () {
		if (!(--this.incorrect)) {
			this._trigger('unlock');
			this.submit.button('enable');
		}
	},
	/**
	 * Handles form @c submit event.
	 * Doesn't do anything if one of following conditions is true:
	 * @li form is locked (see _lock())
	 * @li @ref offline is set to true
	 * @li widget is disabled
	 * 
	 * Otherwise it triggers re-checking of all contained fields and stops if any
	 * are found to be incorrect. If all fields are correct, it either calls
	 * @ref submit function (if set) or sends form using usual POST method.
	 */
	_submit: function () {
		var data = {},
			incorrectField = null;

		if (this.incorrect || this.options.offline || this.options.disabled) {
			return false;
		}

		var fields = this.sets.fieldset('getFields');
		fields.each(function () {
			if ($(this).field('check') && !incorrectField) {
				incorrectField = $(this);
			} else {
                var name = $(this).field('option', 'name');
				data[name] = $(this).field('option', 'value');
                if (data[name] === true)
                {
                    data[name] = "true";
                }
			}
		});
		if (incorrectField) {
			incorrectField.field('setFocus');
			return false;
		}

		if ($.isFunction(this.options.submit)) {
			if (this.options.submit(this.element.get()[0], data) === false) {
				return false;
			}
		}

		return true;
	},
	/**
	 * Calls @ref fieldset::enable() "enable" method of all contained fieldsets
	 * and enables form submit button.
	 * @see disable()
	 */
	enable: function () {
		this.options.disabled = false;
		this.sets.fieldset('enable');
		this.submit.button('enable');
	},
	/**
	 * Calls @ref fieldset::disable() "disable" method of all contained fieldsets
	 * and disables form submit button.
	 * @see enable()
	 */
	disable: function () {
		this.options.disabled = true;
		this.sets.fieldset('disable');
		this.submit.button('disable');
	},
	/**
	 * Fills form fields with supplied values.
	 * @tparam object values field names as keys, values as values
	 */
	fill: function (values) {
		$.each(values, $.proxy(function (name, value) {
			this.getFieldByName(name).field('option', 'value', value);
		}, this));
	},
	/**
	 * Gets all @ref field "fields" managed by contained fieldsets.
	 * @treturn jQuerySel @ref field elements
	 */
	getFields: function () {
		var fields = $();
		this.sets.each(function () {
			fields = fields.add($(this).fieldset('getFields'));
		});
		return fields;
	},
	/**
	 * Gets contained @ref fieldset by value of its @c name attribute.
	 * @tparam string name
	 * @treturn jQueryEl
	 */
	getFieldsetByName: function (name) {
		return this.sets.filter('[name=' + name + ']');
	},
	/**
	 * Gets contained @ref field by @ref field::name "name".
	 * @tparam string name
	 * @treturn jQueryEl
	 */
	getFieldByName: function (name) {
		if (this.index[name] == undefined) {
			var self = this,
				name = name,
				field = null;
			this.sets.each(function () {
				var field = $(this).fieldset('getFieldByName', name);
				if (field) {
					self.index[name] = field;
					return false; // break $.each()
				}
			});
		}
		return this.index[name] || $();
	},
	/**
	 * Gets contained @ref field value by @ref field::name "name".
	 * @tparam string name
	 * @treturn string field value
	 */
	getValueByName: function (name) {
		return this.getFieldByName(name).field('option', 'value');
	}
});