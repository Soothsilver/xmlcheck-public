/**
 * Wraps element in 'panel' (widget content style, round corners) and provides
 * options to set highlight or add icon.
 *
 * @warning Panel style is applied to element wrapper, not the element itself,
 *		therefore jQuerySel methods like %show(), %hide() or %toggle() shouldn't
 *		be used (panel will stay visible). Use widget methods show(), hide() and
 *		toggle() instead.
 */
$.widget('ui.panel', {
	options: {
		/** @type string
		 * panel highlight ('none', 'highlight', or 'error')
		 */
		highlight: 'none',
		/** @type mixed
		 * icon name or null to hide panel icon
		 * If icon is shown, panel gets padded on the left, so that the panel is
		 * clearly labeled with the icon.
		 */
		icon: null
	},
	_create: function () {
		this.element.wrap($('<div></div>').addClass('ui-panel'));
		this.wrapper = this.element.parent('.ui-panel')
			.addClass('ui-widget')
			.addClass('ui-widget-content')
			.corner();
		this.icon = $('<div></div>').icon()
			.prependTo(this.wrapper);
			
		this._setHighlight();
		this._setIcon();
	},
	_setOption: function (key, value) {
		$.Widget.prototype._setOption.call(this, key, value);
		switch (key) {
			case 'highlight':
				this._setHighlight();
				break;
			case 'icon':
				this._setIcon();
				break;
		}
	},
	_setHighlight: function () {
		this.wrapper.removeClass('ui-state-error')
			.removeClass('ui-state-highlight');
		switch (this.options.highlight) {
			case 'highlight':
			case 'error':
				this.wrapper.addClass('ui-state-' + this.options.highlight);
				break;
		}
	},
	_setIcon: function () {
		if (this.options.icon) {
			this.wrapper.addClass('ui-panel-with-icon');
			this.icon.icon('option', 'type', this.options.icon)
				.show();
		} else {
			this.wrapper.removeClass('ui-panel-with-icon');
			this.icon.hide();
		}
	},
	destroy: function () {
		this.icon.icon('destroy')
			.remove();
		this.element.insertAfter(this.wrapper);
		this.wrapper.remove();
	},
	/**
	 * Calls jQuerySel::toggle() method on panel wrapper.
	 */
	toggle: function () {
		this.wrapper.toggle.apply(this.wrapper, arguments);
	},
	/**
	 * Calls jQuerySel::show() method on panel wrapper.
	 */
	show: function () {
		this.wrapper.show.apply(this.wrapper, arguments);
	},
	/**
	 * Calls jQuerySel::hide() method on panel wrapper.
	 */
	hide: function () {
		this.wrapper.hide.apply(this.wrapper, arguments);
	}
});