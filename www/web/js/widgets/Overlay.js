/**
 * Creates overlay over element making its contents inaccessible and partially
 * obscured.
 */
$.widget('ui.overlay', {
	options: {
		/** @type mixed
		 * content to be put on top of overlay and centered horizontally and vertically
		 */
		content: '',
		/** @type array
		 * corner styles as accepted by @ref corner widget
		 */
		corners: ['all']
	},
	_create: function () {
		this._shadeEl = $('<div></div>')
			.addClass('ui-overlay-shade')
			.addClass('ui-widget')
			.addClass('ui-widget-content')
			.addClass('ui-border-transparent')
			.corner({ styles: this.options.corners })
			.appendTo(this.element)
			.hide();
		this._overlayEl = $('<div></div>')
			.addClass('ui-overlay')
			.appendTo(this.element)
			.hide();
		this._centeringEl = $('<div></div>')
			.addClass('ui-overlay-center')
			.appendTo(this._overlayEl);
		this._contentEl = $('<div></div>')
			.addClass('ui-overlay-content')
			.appendTo(this._centeringEl);

		this._baseElements = this._shadeEl.add(this._overlayEl);

		this._refitInterval = window.setInterval($.proxy(this._fitToTarget, this), 100);

		this.option('content', this.options.content);
	},
	_setOption: function (key, value) {
		$.Widget.prototype._setOption.apply(this, arguments);
		switch (key) {
			case 'content':
				this._contentEl.empty().append(value);
				break;
		}
	},
	/**
	 * Resizes overlay so that it covers the element or whole viewport (if necessary).
	 */
	_fitToTarget: function () {
		var targetHeight = this.element.height();
		var windowHeight = $(window).height();
		var height = ((targetHeight > windowHeight) || (this.element.selector == 'body'))
				? windowHeight : targetHeight;
		this._baseElements.css('height', height);
	},
	destroy: function () {
		$(window).unbind('resize.overlay');
		this._baseElements.remove();
		$.Widget.prototype.destroy.call(this);
	},
	/**
	 * Shows overlay using fadeIn animation.
	 * @tparam int speed @optional animation speed (defaults to 'normal')
	 * @tparam function callback @optional to be called after animation finishes
	 */
	show: function (speed, callback) {
		this._fitToTarget();
		this._shadeEl.fadeIn(speed);
		this._overlayEl.fadeIn(speed, callback);
	},
	/**
	 * Hides overlay using fadeOut animation.
	 * @tparam int speed @optional animation speed (defaults to 'normal')
	 * @tparam function callback @optional to be called after animation finishes
	 */
	hide: function (speed, callback) {
		//this._baseElements.stop();
		this._shadeEl.fadeOut(speed);
		this._overlayEl.fadeOut(speed, callback);
	},
	/**
	 * Adds additional content to @ref content.
	 */
	append: function (content) {
		this._contentEl.append(content);
	}
});