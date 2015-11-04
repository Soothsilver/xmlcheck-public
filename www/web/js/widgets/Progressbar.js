/**
 * Turns element into progress bar.
 * @note Original element content is detached from DOM on widget creation to be
 *		returned on widget destruction.
 */
$.widget('ui.progressbar', {
	options: {
		/** @type int
		 * minimum allowed value
		 */
		min: 0,
		/** @type int
		 * maximum allowed value
		 */
		max: 100,
		/** @type int
		 * progress bar value
		 */
		value: 0,
		/** @type int
		 * duration of value-change animation (ms)
		 */
		speed: 200,
		/** @type int
		 * speed of 'progress indication' animation
		 */
		activeSpeed: 400,
		/** @type string
		 * name of easing type (as accepted by jQuerySel animate() method)
		 */
		easing: 'linear',
		/** @type string
		 * text displayed inside progress-bar
		 */
		text: '',
		/** @type bool
		 * set to true to turn on 'progress indication' animation
		 */
		active: false
	},
	_init: function () {
		this._originalContent = this.element.contents().detach();
		this.element.addClass('ui-widget');
		this.parts = {};
		this.parts.bar = $('<div></div>').addClass('ui-progressbar')
			.addClass('ui-widget-content')
			.corner()
			.appendTo(this.element);
		var topOffset = - parseFloat(this.parts.bar.css('border-top-width'));
		var leftOffset = - parseFloat(this.parts.bar.css('border-left-width'));
		this.parts.mask = $('<div></div>').addClass('ui-progressbar-mask')
			.css('top', topOffset + 'px')
			.css('left', leftOffset + 'px')
			.css('clip', 'rect(auto 0px auto auto)')
			.appendTo(this.parts.bar);
		this.parts.fill = $('<div></div>').addClass('ui-progressbar-fill')
			.addClass('ui-widget-header')
			.corner()
			.appendTo(this.parts.mask);
		this._value = 0;
		this.option('min', this.options.min);
		this.option('max', this.options.max);
		this.option('value', this.options.value);
		this.option('text', this.options.text);
		this.option('active', this.options.active);
	},
	_setOption: function (key, value) {
		var oldValue = this.options[key];
		switch (key) {
			case 'min':
				if (value > this.options.max) {
					value = this.options.max;
				}
				break;
			case 'max':
				if (value < this.options.min) {
					value = this.options.min;
				}
				break;
			case 'value':
				if (value < this.options.min) {
					value = this.options.min;
				} else if (value > this.options.max) {
					value = this.options.max;
				}
				break;
		}
		$.Widget.prototype._setOption.call(this, key, value);
		var delayedTrigger = $.noop;
		switch (key) {
			case 'value':
				this._value = this._scaleValue(value);
				if (oldValue != value) {
					var self = this,
						complete = (value == this.options.max);
					delayedTrigger = function () {
						self._trigger('change');
						if (complete) {
							self._trigger('complete');
						}
					};
				}
				// fallthrough to resize
			case 'min': case 'max':
				this._resize(delayedTrigger);
				break;
			case 'text':
				this.parts.bar.add(this.parts.fill)
					.prepend(this.options.text);
				break;
			case 'active':
				if (value) {
					this._startActive();
				}
				break;
		}
	},
	/**
	 * Scales progress bar value to 0-100 scale (percentage).
	 * @tparam int value number between @ref min and @ref max (inclusive)
	 * @treturn int corresponding number on 0-100 scale
	 */
	_scaleValue: function (value) {
		return (((value - this.options.min) * 100 / (this.options.max - this.options.min)) + 1);
	},
	/**
	 * Resizes progressbar filler to reflect current value.
	 * @tparam function callback will be called after resize animation completes
	 */
	_resize: function (callback) {
		this.parts.mask.stop();
		this.parts.mask.animate({clip: 'rect(auto ' + this._value + '% auto auto)'},
			this.options.speed, this.options.easing, callback);
	},
	/**
	 * Starts 'progress indication' animation.
	 * @note Animation will automatically stop when @ref active is set to false.
	 */
	_startActive: function () {
		var self = this;
		var animate = function () {
			var thisFn = arguments.callee;
			$.extend(thisFn, {
				goRight: !thisFn.goRight
			});
			var totalWidth = self._value,
				clipWidth = Math.floor(totalWidth / 3),
				leftTerminal = 'rect(auto ' + clipWidth + '% auto auto)',
				rightTerminal = 'rect(auto ' + totalWidth + '% auto ' + (2 * clipWidth) + '%)';
			self.parts.fill.animate({clip: (thisFn.goRight ? rightTerminal : leftTerminal)}, self.options.activeSpeed, self.options.easing, (self.options.active
				? thisFn
				: function () {
					$(this).animate({clip: 'none'}, self.options.speed, self.options.easing);
				})
			);
		};
		animate();
	},
	destroy: function () {
		this.element.empty();
		this.element.removeClass('ui-widget');
		this._originalContent.appendTo(this.element);
		$.Widget.prototype.destroy.apply(this, arguments);
	}
});