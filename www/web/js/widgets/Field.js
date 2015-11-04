/**
 * Creates form field with extended capabilities inside element.
 * @warning This widget doesn't just enhance existing element. It destroys original
 *		element contents and creates new content from supplied configuration options.
 *		Element will not be returned to original state on widget destruction.
 */
$.widget('ui.field', {
	options: {
		/** @type bool
		 * set to true to disable showing of hint texts next to input field (hints
		 * will be shown as input field @c title attribute)
		 */
		simple: false,
		/** @type mixed
		 * input @c id attribute (string) or null not to set @c id
		 */
		id: null,
		/** @type string
		 * input @c name attribute
		 */
		name: '',
		/** @type object
		 * Set of input value options for 'select', 'checkset', or 'radio' @ref type.
		 * Object property keys stand for option values and property values may be
		 * one of:
		 * @li simple option label (string)
		 * @li array with option label as first element and icon name as second
		 *		(icon will be used only with some @ref type "types" and with @ref fancy
		 *		set to true)
		 */
		options: {},
		/** @type string
		 * input value
		 */
		value: '',
		/** @type string
		 * input label
		 */
		label: '',
		/** @type string
		 * input type (possible values are @c 'empty', @c 'info', @c 'hidden',
		 * @c 'text', @c 'password', @c 'checkbox', @c 'checkset', @c 'radio',
		 * @c 'select', @c 'multiselect', @c 'textarea', @c 'file', and @c 'date')
		 */
		type: 'text',
		/** @type object
		 * additional parameters for @ref type "types" that require them
		 */
		typeParams: {},
		/** @type bool
		 * if set to false, field cannot be edited, but its value is still sent on
		 * form submit (it's not sent when the field is disabled)
		 */
		editable: true,
		/** @type bool
		 * set to true to turn on fancy field looks for some @ref type "types"
		 * ('checkset', 'radio', 'date', 'checkbox')
		 */
		fancy: false,
		/** @type string
		 * hint to be shown next to field before and while field is being edited
		 * (it is replaced by either 'check' icon or error message when editing
		 * is completed)
		 */
		hint: '',
		/** @type string
		 * class to be applied to input on focus
		 */
		focusClass: 'ui-state-highlight',
		/** @type mixed
		 * Check that should be performed on input value to determine whether it is
		 * allowed in this input. Can be one of following types:
		 * @li name of predefined checking function (see @ref validators)
		 * @li custom checking function
		 * @li array containing more of the above two (performs all checks)
		 * @li @c null to perform no checking (value is always allowed)
		 */
		check: null,
		/** @type object
		 * additional parameters for @ref check "checks" that require them
		 */
		checkParams: {},
		/** @type string
		 * class to be applied to input on error (value not allowed)
		 */
		errorClass: 'ui-state-error'
	},
	_create: function () {
		// extract options from current content
		var inputs, input,
			target = this.element,
			o = this.options;
		if ((inputs = target.find('input')).length) {
			input = inputs.first();
			$.extend(o, {
				value: input.val(),
				name: input.attr('name'),
				type: input.attr('type'),
				options: {}
			});
			switch (o.type) {
				case 'checkbox':
					o.options[o.value] = '';
					o.value = input.attr('checked');
					break;
				case 'select':
					if (input.attr('multiple')) {
						o.type = 'multiselect';
					}
					input.children('option').each(function () {
						o.options[$(this).attr('value')] = $(this).attr('text');
					});
					break;
				case 'radio':
					inputs.filter(':radio[name=' + o.name + ']').each(function () {
						o.options[$(this).val()] = target.find('label[for=' + $(this).attr('id') + ']').text();
					});
					break;
			}
		} else if ((inputs = target.find('textarea')).length) {
			input = inputs.first();
			$.extend(o, {
				value: input.val(),
				name: input.attr('name'),
				type: 'textarea'
			});
		}
		if (input != undefined) {
			o.label = target.find('label[for=' + input.attr('id') + ']').text();
		}
		if (o.id === null) {
			o.id = o.name;
		}

		// create new content
		this.element.empty()
			.addClass('ui-field')
			.addClass('ui-helper-clearfix')
			.bind('focusin.field', $.proxy(this._focus, this))
			.bind('focusout.field', $.proxy(this._blur, this));

		var labelContainer = $('<div></div>')
			.addClass('ui-field-label')
			.appendTo(target);
		this.inputContainer = $('<div></div>')
			.addClass('ui-field-input')
			.appendTo(target);
		var visualAidContainer = $('<div></div>')
			.addClass('ui-field-help')
			.appendTo(target);
		
		this.label = $('<label></label>')
			.attr('for', this.options.id)
			.appendTo(labelContainer);

		this.visualAidIcon = $('<div></div>')
			.icon()
			.addClass('ui-icon-label')
			.appendTo(visualAidContainer);

		this.visualAidText = $('<div></div>')
			.addClass('ui-field-help-text')
			.appendTo(visualAidContainer);
			
		this.input = $();
		this.inputParts = [];
		this.error = false;
		this.visualAidState = null;
		this.untouched = true;

		this.updateDefaultState();
		this._initAll();
	},
	/**
	 * Initializes look & behavior using widget configuration.
	 */
	_initAll: function () {
		this._setSimple();
		this._setLabel();
		this._setType();
		this._setOptions();
		this._setValue();
		this._setEditable();
		this._setId();
		this._setName();
		this._setSpecial();
		this._setFancy();
		this._bindChange();
		this._showVisualAid();
	},
	_init: function () {
	},
	_setOption: function (key, value) {
		var oldValue = this.options[key];
		$.Widget.prototype._setOption.apply(this, arguments);
		switch (key) {
			case 'simple':
				this._setSimple();
				this._showVisualAid(true);
				break;
			case 'id':
				this._setId();
				break;
			case 'name':
				this._setName();
				break;
			case 'value':
				this._setValue();
				this._setFancy();
				this.untouched = true;
				this._setError(false);
				break;
			case 'label':
				this._setLabel();
				break;
			case 'type':
				this._setType();
				// fallthrough to options
			case 'options':
				this._setOptions();
				this._setId();
				this._setName();
				this._setValue();
				this._setSpecial();
				this._bindChange();
				this._setEditable();
				// fallthrough to fancy
			case 'fancy':
				this._setFancy();
				break;
			case 'editable':
				this._setEditable();
				this._showVisualAid();
				break;
			case 'hint': case 'hintClass': case 'errorClass':
				this._showVisualAid(true);
				break;
		}
	},
	_setSimple: function () {
		this.element[(this.options.simple ? 'add' : 'remove') + 'Class']('ui-field-simple');
	},
	_setLabel: function () {
		this.label.html(this.options.label);
	},
	_setType: function () {
		this.input.remove();
		this.element.show();

		var selectThis = function (event) {
				$(event.currentTarget).select();
			},
			types = ['empty', 'info', 'hidden', 'text', 'password', 'checkbox',
				'checkset', 'radio', 'select', 'multiselect', 'textarea', 'file', 'date'];

		for (var i in types) {
			this.element.removeClass('ui-field-type-' + types[i]);
		}
		this.element.addClass('ui-field-type-' + this.options.type);

		switch (this.options.type) {
			case 'empty': case 'info':
				this.input = $('<div></div>')
					.addClass('ui-field-input-info')
					.addClass('ui-border-transparent')
					.addClass('ui-background-transparent');
				break;
			case 'hidden':
				this.element.hide();
				this.input = $('<input type="hidden"/>');
				break;
			case 'text':
				this.input = $('<input type="text"/>')
					.focus(selectThis);
				break;
			case 'password':
				this.input = $('<input type="password" value="' + this.options.value + '"/>')
					.focus(selectThis);
				break;
			case 'checkbox':
				this.input = $('<input type="checkbox"/>');
				break;
			case 'checkset': case 'radio':
				this.input = $('<div></div>')
					.addClass('ui-border-transparent')
					.addClass('ui-background-transparent')
					.addClass('ui-radio-set');
				break;
			case 'select':
				this.input = $('<select></select>');
				break;
			case 'multiselect':
				this.input = $('<select multiple></select>');
				break;
			case 'textarea':
				this.input = $('<textarea></textarea>');
				break;
			case 'file':
				var selectFileButton = $('<div></div>')
					.addClass('ui-field-file-overlay-button')
					.button({icons: {primary: 'ui-icon-folder-open'}});
				this.input = $('<div></div>')
					.addClass('ui-field-file-wrapper')
					.addClass('ui-border-transparent')
					.addClass('ui-background-transparent')
					.append($('<div></div>').addClass('ui-field-file-overlay')
						.append($('<div></div>').addClass('ui-field-file-overlay-text')
							.addClass('ui-widget-content')
							.addClass('ui-background-solid'))
						.append(selectFileButton))
					.append($('<input type="file"/>'));
				break;
			case 'date':
				this.input = $('<input type="text"/>');
				break;
		}

		this.input.addClass('ui-widget-content')
			.addClass('ui-background-solid')
			.appendTo(this.inputContainer);
	},
	/**
	 * Turns options from 'lazy' input format (see @ref options) to unified object
	 * format.
	 * @treturn array options have following properties: @c value, @c label, and @c icon
	 */
	_getParsedOptions: function () {
		var options = [],
			simpleOptions = $.isArray(this.options.options);
		$.each(this.options.options, function (value, label) {
			if (simpleOptions) {
				value = label;
			} else {
				var hasIcon = $.isArray(label);
			}
			options.push({
				value: value,
				label: hasIcon ? label[0] : label,
				icon: hasIcon ? label[1] : null
			});
		});
		return options;
	},
	_setOptions: function () {
		var options = this._getParsedOptions(),
			input = this.input,
			type = this.options.type;
		switch (type) {
			case 'checkbox':
				input.val('');
				$.each(options, function (i, opt) {
					if (!opt.label) {
						input.val(opt.value);
					}
				});
				break;
			case 'select':
			case 'multiselect':
				this.input.empty();
				$.each(options, function (i, opt) {
					$('<option></option>')
						.attr('value', opt.value)
						.appendTo(input)
						.text(opt.label);
				});
				if (type == 'multiselect') {
					this.inputDouble = $('<input type="hidden"/>')
						.attr('value', '')
						.appendTo(this.input);
				}
				break;
			case 'radio': case 'checkset':
				this.input.empty();
				var parts = this.inputParts = [];
				$.each(options, function (i, opt) {
					var line = $('<div></div>')
						.addClass('ui-field-line')
						.appendTo(input);
					var box = $('<input type="' + ((type == 'checkset') ? 'checkbox' : type) + '"/>')
						.attr('value', opt.value)
						.appendTo(line);
					var label = $('<label></label>')
						.html(opt.label)
						.appendTo(line);
					parts.push({
						input: box,
						label: label,
						icon: opt.icon
					});
				});
				if (type == 'checkset') {
					this.inputDouble = $('<input type="hidden"/>')
						.attr('value', '')
						.appendTo(this.input);
				}
				break;
		}
	},
	_setId: function () {
		var id = this.options.id;
		this.input.attr('id', id);
		if ((this.options.type == 'radio') || (this.options.type == 'checkset')) {
			$.each(this.inputParts, function (i, part) {
				var newId = [id, part.input.attr('value')].join('-');
				part.input.attr('id', newId);
				part.label.attr('for', newId)
			});
		}
	},
	_setName: function () {
		var o = this.options;
		switch (o.type) {
			case 'radio':
				$.each(this.inputParts, function (i, part) {
					part.input.attr('name', o.name);
				});
				break;
			case 'checkset':
			case 'multiselect':
				this.inputDouble.attr('name', o.name);
				break;
			case 'file':
				$(':file', this.input).attr('name', o.name);
				break;
			default:
				this.input.attr('name', o.name);
		}
	},
	/**
	 * Initializes special properties of more unusual input types (like @c 'date').
	 */
	_setSpecial: function () {
		switch (this.options.type) {
			case 'date':
				this.input.datepicker('destroy')
					.datepicker({
						dateFormat: 'yy-mm-dd'
					});
				break;
		}
	},
	_setValue: function () {
		var o = this.options;
		switch (o.type) {
			case 'info':
				this.input.html(o.value);
				break;
			case 'hidden': case 'text': case 'date':
				this.input.val(o.value);
				break;
            case 'checkbox':
                if (o.value) { this.input.attr('checked', 'checked');}
                else { this.input.removeAttr('checked'); }
                break;
			case 'select':
				var options = this._getParsedOptions(),
					found = false,
					value = null;
				$.each(options, function (i, option) {
					if (o.value == option.value) {
						found = true;
						return false;	// break $.each()
					} else if ((value == null) && (o.value == option.label)) {
						value = option.value;
					}
				});
				if (!o.value) {
					o.value = options[0] ? options[0].value : null;
				} else if (!found && (value != null)) {
					o.value = value;
				}
				this.input.val(o.value);
				break;
			case 'multiselect':
				var options = this._getParsedOptions(),
					givenValues = o.value.split(';'),
					values = [];
				$.each(options, function (i, option) {
					$.each(givenValues, function (j, value) {
						if (value == option.value) {
							values.push(value);
							return false;	// break $.each()
						}
					});
				});
				this.input.val(values);
				o.value = values.join(';');
				this.inputDouble.val(o.value);
				break;
			case 'radio':
				this.input.find(':radio[id=' + o.id + '-' + o.value + ']')
					.attr('checked', true);
				break;
			case 'checkset':
				var givenValues = o.value.split(';'),
					values = [];
				$.each(this.inputParts, function (i, part) {
					var value = part.input.attr('value'),
						checked = false;
					for (var j in givenValues) {
						if (value == givenValues[j]) {
							checked = true;
							values.push(value);
							break;
						}
					}
					part.input.attr('checked', checked);
				});
				o.value = values.join(';');
				this.inputDouble.val(o.value);
				break;
			case 'textarea':
				this.input.text(o.value);
				break;
			case 'file':
				$('.ui-field-file-input-text', this.input).text($(':file', this.input).val());
				break;
		}
	},
	/**
	 * Sets internal error property, triggers appropriate event (@c 'correct'
	 * or @c 'incorrect') if error state changed, and displays error.
	 * @note Doesn't work when widget is disabled.
	 */
	_setError: function (error) {
		if (!this.options.disabled) {
			if (error != this.error) {
				if (!!error != !!this.error) {
					this._trigger(error ? 'incorrect' : 'correct');
				}
				this.error = error;
			}
			this._showVisualAid();
		}
	},
	_setFancy: function () {
		var self = this,
			o = this.options,
			fancyInputPartsBasic = function () {
				$.each(self.inputParts, function (i, part) {
					part.input.button({
						text: false,
						icons: {
							primary: 'ui-icon-' + part.icon,
							secondary: null
						}
					});
					part.input.bind('change.field-fancy', function (event) {
						$(event.currentTarget).focus().blur();
					});
				})
			};
		this.input.buttonset('destroy');
		$.each(this.inputParts, function (i, part) {
			part.input.button('destroy');
			part.input.unbind('change.field-fancy');
		});
		this.element.removeClass('ui-fancy');
		if (o.fancy) {
			this.element.addClass('ui-fancy');
			switch (o.type) {
				case 'select':
					// will have fancy mode when jQuery UI spinner widget comes out
					break;
				case 'checkbox':
					break;
				case 'radio':
					this.input.buttonset();
					fancyInputPartsBasic();
					break;
				case 'checkset':
					$.each(this.inputParts, function (i, part) {
						part.input.button();
					});
					fancyInputPartsBasic();
					break;
			}
		}
	},
	_setEditable: function () {
		var o = this.options,
			inputs = $();
		switch (o.type) {
			case 'hidden': case 'text': case 'password': case 'checkbox':
			case 'select': case 'textarea':
				inputs = this.input;
				break;
			case 'multiselect':
				inputs = this.input.add(this.inputDouble);
				break;
			case 'radio': case 'checkset': case 'file':
				inputs = $('input', this.input);
				break;
		}
		
		if (o.editable) {
			if (!o.disabled) {
				inputs.removeAttr('disabled');
			}
		} else {
			inputs.attr('disabled', true);
		}
	},
	/**
	 * Binds callbacks updating @ref value to events related to field editing done
	 * by user.
	 */
	_bindChange: function () {
		var o = this.options;
		switch (o.type) {
			case 'text': case 'password':case 'select':
			case 'textarea': case 'date':
				this.input.unbind('change.field')
					.bind('change.field', $.proxy(function (event) {
						o.value = $(event.currentTarget).val();
					}, this))
					.bind('change.field', $.proxy(this._change, this));
				break;
            case 'checkbox':
                this.input.unbind('change.field')
                    .bind('change.field', $.proxy(function (event) {
                        o.value = event.currentTarget.checked;
                    }, this))
                    .bind('change.field', $.proxy(this._change, this));
                break;
            case 'multiselect':
				this.input.unbind('change.field')
					.bind('change.field', $.proxy(function (event) {
						var values = $(event.currentTarget).val();
						o.value = values.join(';');
						this.inputDouble.attr('value', o.value);
					}, this))
					.bind('change.field', $.proxy(this._change, this));
				break;
			case 'checkset':
				for (var i in this.inputParts) {
					this.inputParts[i].input.unbind('change.field-value')
						.bind('change.field-value', $.proxy(function (event) {
							var target = $(event.currentTarget),
								values = o.value ? o.value.split(';') : [],
								value = target.attr('value');
							if (target.is(':checked')) {
								values.push(value);
							} else {
								values.splice($.inArray(value, values), 1);
							}
							o.value = values.join(';');
							this.inputDouble.attr('value', o.value);
						}, this));
				}
				break;
			case 'radio':
				for (var i in this.inputParts) {
					this.inputParts[i].input.unbind('change.field-value')
						.bind('change.field-value', $.proxy(function (event) {
							var target = $(event.currentTarget);
							if (target.is(':checked')) {
								o.value = target.val();
							}
						}, this));
				}
				break;
			case 'file':
				$(':file', this.input)
					.unbind('mouseenter.field-value mouseleave.field-value click.field-value change.field-value')
					.bind('mouseenter.field-value mouseleave.field-value click.field-value', $.proxy(function (event) {
						$('.ui-field-file-overlay-button', this.input).trigger(event.type);
					}, this))
					.bind('change.field-value', $.proxy(function () {
						o.value = $(':file', this.input).val();
						$('.ui-field-file-overlay-text', this.input).text(o.value);
					}, this))
					.bind('change.field-value', $.proxy(this._change, this));
				break;
		}
	},
	/**
	 * Shows that input element is selected (applies @ref focusClass).
	 * @note Doesn't work if field is in error state.
	 */
	_focus: function () {
		this.hasFocus = true;
		this._showVisualAid();
	},
	/**
	 * Shows that input element is not selected (removes @ref focusClass).
	 * @note Doesn't work if field is in error state.
	 */
	_blur: function () {
		this.hasFocus = false;
		this._showVisualAid();
	},
	/**
	 * Handles input value change (checks value and shows visual feedback).
	 */
	_change: function () {
		this._check();
		this._showVisualAid();
	},
	/**
	 * Shows visual aid appropriate for field state.
	 * That is either initial hint, error hint, or 'check' icon.
	 * @tparam bool forceRefresh @optional set to true to force visual aid redraw
	 * @see hint
	 * @see simple
	 */
	_showVisualAid: function (forceRefresh) {
		var state;
		if (this.options.disabled || !this.options.editable) {
			state = null;
		} else if (this.error) {
			state = 'error';
		} else if (this.hasFocus) {
			state = 'focus';
		} else if (this.untouched) {
			state = 'untouched';
		} else {
			state = 'check';
		}
		if ((state != this.visualAidState) || (state == 'error') || forceRefresh) {
			this.visualAidState = state;
			this.element.removeClass(this.options.errorClass);
			this.visualAidText.removeClass('ui-field-hint');
			var fileNameText = $('.ui-field-file-overlay-text', this.input)
					.text(this.options.value),
				focusElements = this.input.add(fileNameText)
					.removeClass(this.options.focusClass),
				helpText = '',
				helpIcon = false;
			switch (this.visualAidState) {
				case 'error':
					helpText = this.error;
					this.element.addClass(this.options.errorClass);
					if (this.hasFocus) {
						focusElements.addClass(this.options.focusClass);
					}
					break;
				case 'focus':
					focusElements.addClass(this.options.focusClass);
					if (!this.options.value && fileNameText) {
						fileNameText.text(asm.lang.general.clickHere);
					}
					// fallthrough to show hint
				case 'untouched':
					helpText = this.options.hint;
					if (helpText) {
						this.visualAidText.addClass('ui-field-hint');
					}
					break;
				case 'check':
					helpIcon = 'check';
					break;
			}

			this.visualAidIcon.icon('option', 'type', helpIcon);
			this.visualAidText.html(helpText);
			this.input.attr('title', this.options.simple ? helpText : '');
		}
	},
	/**
	 * Performs field value checks based on widget configuration and sets appropriate
	 * error if necessary.
	 */
	_check: function () {
		this.untouched = false;
		var self = this,
			error = false,
			checks = $.isArray(this.options.check) ? this.options.check : [this.options.check];
		$.each(checks, function (i, check) {
			if ($.isFunction(check)) {
				error = check(self.options.value, self.element);
			} else {
				var validator = $.ui.field.validators[check];
				if (validator != undefined) {
					error = validator.call($.ui.field.validators, self.options.value, self.options.checkParams);
				}
			}
			if (error) {
				return false; // break $.each()
			}
		});
		this._setError(error);
	},
	/**
	 * Handles field enabling/disabling (makes input field(s) disabled and hides
	 * visual aid).
	 * @tparam bool disable true to disable field, false to enable it
	 */
	_disable: function (disable) {
		this.options.disabled = disable;
		this.element.toggleClass('ui-field-disabled', disable);
		var elems = $();
		switch (this.options.type) {
			case 'radio':
				elems = this.input.find(':radio');
				break;
			case 'file':
				elems = $(':file', this.input);
				$('.ui-field-file-overlay-button', this.input).button(disable ? 'disable' : 'enable');
				break;
			default:
				elems = this.input;
		}
		if (disable) {
			elems.attr('disabled', true);
		} else {
			elems.removeAttr('disabled');
		}
		this.label.toggleClass('ui-state-disabled', disable);
		this._showVisualAid();
	},
	/**
	 * Enables field.
	 */
	enable: function () {
		this._disable(false);
		this._setEditable();
	},
	/**
	 * Disables field.
	 */
	disable: function () {
		this._disable(true);
	},
	/**
	 * Sets focus to contained element (or first of them in case of some @ref type "types").
	 */
	setFocus: function () {
		switch (this.options.type) {
			case 'text': case 'password': case 'checkbox': case 'select':
			case 'multiselect': case 'textarea':
				this.input.focus();
				break;
			case 'radio':
				this.input.find(':radio:first-child').focus();
				break;
		}
	},
	/**
	 * Sets field error programatically.
	 * @tparam string message
	 */
	setError: function (message) {
		this._setError(message);
	},
	/**
	 * Clears field error programatically.
	 */
	clearError: function () {
		this._setError(false);
	},
	/**
	 * Checks field as defined in widget configuration.
	 * @treturn mixed either @c false if @ref editable is set to false, field type
	 *		doesn't support checking (@c empty, @c info, and @c hidden types), or if
	 *		checking finished without an error; error message string otherwise
	 */
	check: function () {
		if (!this.options.editable) {
			return false;
		}
		switch (this.options.type) {
			case 'empty': case 'info': case 'hidden':
				return false;
			default:
				this._check();
				this._showVisualAid();
				return this.error;
		}
	},
	/**
	 * Clears input value in fashion appropriate to @ref type.
	 */
	clear: function () {
		switch (this.options.type) {
			case 'checkbox':
				this.input.attr('checked', false);
				// fallthrough to clearing value
			default:
				this.option('value', '');
		}
	},
	/**
	 * Sets current widget configuration as defaults.
	 * @see reset()
	 */
	updateDefaultState: function () {
		this.defaults = $.extend({}, this.options);
	},
	/**
	 * Resets widget configuration to defaults.
	 * 'Defaults' means configuration supplied on widget creation or updated using
	 * updateDefaultState() method.
	 */
	reset: function () {
		this.options = $.extend({}, this.defaults);
		this.untouched = true;
		this._setError(false);
		this._initAll();
	}
});

$.fn.extend($.ui.field, {
	/** @type object
	 * Predefined field value checking functions.
	 * @li @c isNumber value must be a number
	 * @li @c isNumeric value must consist only of digits
	 * @li @c isAlphabecit value must consist only of basic letters (a-z, A-Z)
	 * @li @c isAlphaNumeric value must consists only of basic letters or digits
	 * @li @c isName value must consist only of basic or Czech letters
	 * @li @c isEmail value must be a valid e-mail address
	 * @li @c isDate value must be a date formatted as MySQL timestamp
	 * @li @c hasMinLength value must have minimum length of <tt>params.minLength</tt>
	 * @li @c hasMaxLength value must have maximum length of <tt>params.maxLength</tt>
	 * @li @c hasLength value must have minimum length of <tt>params.minLength</tt>
	 *		and/or maximum length of <tt>params.maxLength</tt>
	 *	@li @c hasExtension value must end with a dot followed by one of values
	 *		from <tt>params.extensions</tt>
	 *	@li @c isGreaterThan value must be greater than or equal to <tt>params.min</tt>
	 *	@li @c isLessThan value must be less than or equal to <tt>params.max</tt>
	 *	@li @c inRange value must be numeric, greater than or equal to <tt>params.min</tt>
	 *		and less than or equal to <tt>params.max</tt>
	 *	@li @c isNotEmpty value must not be an empty string (consisting only of whitespace)
	 *	@li @c isNotEqualTo value must not be equal to <tt>params.value</tt>
	 *	@li @c isNonNegativeNumber value must be a non-negative number
	 *
	 *	where @c params is value of @ref checkParams.
	 */
	validators: {
		isNumber: function (value) {
			return (((value - 0) != value) || (value.length == 0))
				? asm.lang.checks.mustBeANumber : false;
		},
		isNumeric: function (value) {
			return (value.search(/[^0-9]/) != -1)
				? asm.lang.checks.mustBeNumeric : false;
		},
		isAlphabetic: function (value) {
			return (value.search(/[^a-zA-Z]/) != -1)
				? asm.lang.checks.mustBeAlphabetic : false;
		},
		isAlphaNumeric: function (value) {
			return (value.search(/[^a-zA-Z0-9]/) != -1)
				? asm.lang.checks.mustBeAlphanumeric : false;
		},
		isName: function (value) {
			return (value.search(/[^0-9a-zA-Zžščřďťňáéíóúůýě ]/) != -1)
				? asm.lang.checks.mustContainOnlyLettersAndSpaces : false;
		},
		isEmail: function thisFn (value) {
			if (thisFn.email == undefined) {
				thisFn.email = /^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i;
			}
			return (!thisFn.email.test(value))
				? asm.lang.checks.mustBeEmail : false;
		},
		isDate: function (value) {
			var parts = value.split('-'),
				prefixed = (parts[1].length == 2) && (parts[2].length == 2),
				year = parseInt(parts[0], 10),
				month = parseInt(parts[1], 10) - 1,
				day = parseInt(parts[2], 10),
				date = new Date(year, month, day);
			return (!prefixed || (date.getFullYear() != year) || (date.getMonth() != month)
					|| (date.getDate() != day))
				? asm.lang.checks.mustBeDate : false;
		},
		hasMinLength: function (value, params) {
			return ((params.minLength != undefined) && (value.length < params.minLength))
				? asm.lang.checks.mustBeAtLeast + params.minLength : false;
		},
		hasMaxLength: function (value, params) {
			return ((params.maxLength != undefined) && (value.length > params.maxLength))
				? asm.lang.checks.mustBeAtMost + params.maxLength : false;
		},
		hasLength: function (value, params) {
			return this.hasMinLength(value, params) || this.hasMaxLength(value, params);
		},
		hasExtension: function (value, params) {
			var extensions = params.extensions,
				msg = false;
			if (extensions != undefined) {
				msg = asm.lang.checks.mustHaveExtension + extensions.join(', ');
				for (var i in extensions) {
					var regexp = new RegExp('\\.' + extensions[i] + '$');
					if (regexp.test(value)) {
						msg = false;
					}
				}
			}
			return msg;
		},
		isGreaterThan: function (value, params) {
			return ((params.min != undefined) && (value < params.min))
				? asm.lang.checks.mustBeGreater + params.min  : false;
		},
		isLessThan: function (value, params) {
			return ((params.max != undefined) && (value > params.max))
				? asm.lang.checks.mustBeLower + params.max  : false;
		},
		inRange: function (value, params) {
			return this.isNumeric(value) || this.isGreaterThan(value, params) || this.isLessThan(value, params);
		},
		isNotEmpty: function (value) {
			value += '';
			return (!value || (value.replace(/^\s+|\s+$/g, '').length == 0))
				? asm.lang.checks.mustNotBeEmpty : false;
		},
		isNotEqualTo: function (value, params) {
			return ((params.value != undefined) && (value == params.value))
				? asm.lang.checks.thisValueIsNotAllowed: false;
		},
		isNonNegativeNumber: function (value) {
			return this.isNumber(value)
				|| ((value < 0) ? asm.lang.checks.mustBeNonNegative : false);
		}
	}
});