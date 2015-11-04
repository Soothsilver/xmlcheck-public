/**
 * Enhances fieldset to manage contained @ref field widgets.
 *
 * @note Must be called on jQuerySel consisting only of <tt>&lt;fieldset&gt;</tt>
 *		elements.
 *	@note Element children must all be @ref field widgets already, otherwise the
 *		widgets will be created from them.
 */
$.widget('ui.fieldset', {
	options: {
		/** @type bool
		 * set to true to turn on 'blending' mode (fielset border and label are
		 * hidden, fieldset blends into surroundings)
		 */
		blend: false,
		/** @type mixed
		 * icon shown before the fieldset label text (icon name or false to show
		 * no icon)
		 * @see icon
		 */
		icon: false,
		/** @type string
		 * fieldset label (== legend)
		 * @note No label is shown if @ref label is empty and @ref icon is set to
		 *		false.
		 */
		label: '',
		/** @type bool
		 * passed as @ref field::simple "simple" option to contained fields
		 */
		simple: false
	},
	_create: function () {
		var self = this;
		this.index = {};
		this.element.addClass('ui-helper-reset')
			.wrap('<div></div>')
			.wrap('<div></div>');
		this.wrapper = this.element.parent()
			.addClass('ui-fieldset')
			.addClass('ui-widget')
			.addClass('ui-widget-content')
			.corner({ styles: ['bottom'] });
		this.padder = this.wrapper.parent()
			.addClass('ui-fieldset-wrapper');
		this.fields = this.element.children()
			.field()
			.each(function () {
				self.index[$(this).field('option', 'name')] = $(this);
			});
		var sourceLabels = this.element.children('legend');
		if (sourceLabels.length) {
			this.options.label = sourceLabels.first().text();
			sourceLabels.remove();
		}
		var legend = $('<legend></legend>')
			.prependTo(this.element);
		this.header = $('<div></div>')
			.addClass('ui-fieldset-header')
			.addClass('ui-state-default')
			.appendTo(legend);
		this.icon = $('<div></div>')
			.addClass('ui-icon-label')
			.prependTo(this.header)
			.icon();
		this.caption = $('<span></span>')
			.appendTo(this.header);
		this.label = $('<span></span>')
			.appendTo(this.header);

		this._setBlend();
		this._setIcon();
		this._setLabel();
		this._setSimple();

		this.incorrect = 0;
		this.fields.bind('fieldincorrect.fieldset', $.proxy(function () {
				if (!(this.incorrect++)) {
					this._trigger('incorrect');
				}
			}, this))
			.bind('fieldcorrect.fieldset', $.proxy(function () {
				if (!(--this.incorrect)) {
					this._trigger('correct');
				}
			}, this));
	},
	_setOption: function (key, value) {
		$.Widget.prototype._setOption.call(this, key, value);
		switch (key) {
			case 'blend':
				this._setBlend();
				break;
			case 'icon':
				this._setIcon();
				break;
			case 'label':
				this._setLabel();
				break;
			case 'simple':
				this._setSimple();
				break;
		}
	},
	_setBlend: function () {
		var blend = this.options.blend;
		this.header.corner({ styles: [blend ? 'all' : 'top'] });
		this.wrapper.toggleClass('ui-border-transparent', blend);
	},
	_setIcon: function () {
		var icon = this.options.icon;
		this.icon.icon('option', 'type', icon);
	},
	_setLabel: function () {
		var label = this.options.label;
		this.caption.html(label);
		this.padder[(label ? 'add' : 'remove') + 'Class']('ui-fieldset-with-legend');
		this.wrapper.corner('remove', 'top');
	},
	_setSimple: function () {
		this.fields.field('option', 'simple', this.options.simple);
	},
	/**
	 * Enables contained fields.
	 * @see disable()
	 */
	enable: function () {
		this.fields.field('enable');
	},
	/**
	 * Disables contained fields.
	 * @see enable()
	 */
	disable: function () {
		this.fields.field('disable');
	},
	/**
	 * Gets contained field with supplied @ref field::name "name".
	 * @tparam string name
	 * @treturn jQueryEl field widget element
	 * @see getFields()
	 */
	getFieldByName: function (name) {
		return (this.index[name] || null);
	},
	/**
	 * Gets all contained fields.
	 * @treturn jQuerySel field widget elements
	 * @see getFieldByName()
	 */
	getFields: function () {
		return this.fields;
	}
});