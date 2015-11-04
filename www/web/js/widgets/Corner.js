/**
 * Adds round corners to elements.
 */
$.widget('ui.corner', {
	options: {
		/** corner class prefix (set to use this widget for different purpose) */
		prefix: 'ui-corner-',
		/**
		 *	determines displayed corners
		 *	(possible values: @c tl, @c top, @c tr, @c right, @c br, @c bottom,
		 *	@c bl, @c left, @c all)
		 */
		styles: ['all']
	},
	_create: function () {
		this.partials = ['tl', 'top', 'tr', 'right', 'br', 'bottom', 'bl', 'left'];
		this.option('styles', (this.options.styles == null) ? [] : this.options.styles);
		this.defaultStyles = this.options.styles;
	},
	_setOption: function (key, value) {
		switch (key) {
			case 'styles':
				if (!value) {
					value = [];
				}
				break;
		}
		$.Widget.prototype._setOption.call(this, key, value);
		switch (key) {
			case 'styles':
				this._setStyles();
				break;
		}
	},
	_setStyles: function () {
		var prefix = this.options.prefix,
			element = this.element.removeClass(prefix + 'all');
		$.each(this.partials, function (i, style) {
			element.removeClass(prefix + style);
		});
		$.each(this._computeStyles(this.options.styles), function (i, style) {
			element.addClass(prefix + style);
		});
	},
	/**
	 * Determines set of style classes to be used based on supplied styles array.
	 * @tparam array styles (formatted same as @ref styles option)
	 */
	_computeStyles: function (styles) {
		var computedStyles;
		if ($.inArray('all', styles) != -1) {
			computedStyles = ['all'];
		} else {
			var indexes = [];
			$.each(this.partials, function (i, style) {
				if ($.inArray(style, styles) != -1) {
					indexes.push(i);
				}
			});
			if (indexes.length) {
				var base = indexes[0],
					edge = base % 2,
					divergence = indexes[indexes.length - 1] - base;
				if (edge) {
					switch (divergence) {
						case 0: case 1:
							computedStyles = [this.partials[base]];
							break;
						case 2: case 3:
							computedStyles = [this.partials[(base + 7) % 8], this.partials[(base + 1) % 8], this.partials[(base + 3) % 8]];
							break;
						default:
							computedStyles = ['all'];
					}
				} else {
					switch (divergence) {
						case 0:
							computedStyles = [this.partials[base]];
							break;
						case 1: case 2:
							computedStyles = [this.partials[base + 1]];
							break;
						case 3: case 4:
							computedStyles = [this.partials[base], this.partials[(base + 2) % 8], this.partials[(base + 4) % 8]];
							break;
						default:
							computedStyles = ['all'];
					}
				}
			} else {
				computedStyles = [];
			}
		}
		return computedStyles;
	},
	/**
	 * Adds supplied style to @ref styles.
	 * @tparam string style (see @ref styles for possible values)
	 */
	add: function (style) {
		var i = $.inArray(style, this.options.styles);
		if (i == -1) {
			this.options.styles.push(style);
			this._setStyles();
		}
	},
	/**
	 * Removes supplied style from @ref styles.
	 * @tparam string style
	 */
	remove: function (style) {
		var i = $.inArray(style, this.options.styles);
		if (i != -1) {
			this.options.styles.splice(i, 1);
			this._setStyles();
		}
	},
	/**
	 * Revert value of @ref styles to the one set on widget creation.
	 */
	revert: function () {
		this.option('styles', this.defaultStyles);
		return this.element;
	}
});