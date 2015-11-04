/**
 * Turns element into simplistic 'progress indicator'.
 * Progress indicator is styled like widget header and consists of single icon
 * and text label. Icon type is cycled to show that something is happening.
 * @note Original element content is detached from DOM on widget creation to be
 *		returned on widget destruction.
 */
$.widget('ui.progressIndicator', {
	options: {
		/** @type mixed
		 * progress indicator label (string) or null to show no label
		 */
		text: null,
		/** @type int
		 * time before icon is changed (ms)
		 */
		speed: 150,
		/** @type string
		 * prefix for icon names
		 */
		iconPrefix: 'arrowrefresh-1-',
		/** @type array
		 * icon names progress indicator should cycle between (stripped of @ref iconPrefix)
		 */
		iconSuffixes: ['n', 'e', 's', 'w']
	},
	_create: function () {
		this._originalContent = this.element.contents().detach();
		this.options.text = this.options.text || this.element.text();
		this.element.addClass('ui-widget')
			.addClass('ui-widget-header')
			.addClass('ui-progressIndicator')
			.corner();
		this.icon = $('<div></div>').icon()
			.addClass('ui-progressIndicator-icon')
			.appendTo(this.element);
		this.state = 0;
		this.label = $('<div></div>')
			.append(this.options.text)
			.appendTo(this.element);
		this.option('speed', this.options.speed);
		this._step();
	},
	_setOption: function (key, value) {
		$.Widget.prototype._setOption.call(this, key, value);
		switch (key) {
			case 'text':
				this.label.html(value);
				break;
			case 'speed':
				if (this.interval) {
					clearInterval(this.interval);
				}
				var self = this;
				this.interval = setInterval(function () {
					self._step.call(self);
				}, value);
				break;
		}
	},
	/**
	 * Change icon to next in line.
	 * @see iconSuffixes
	 */
	_step: function () {
		++this.state;
		this.state %= this.options.iconSuffixes.length;
		this.icon.icon('option', 'type', this.options.iconPrefix + this.options.iconSuffixes[this.state]);
	},
	destroy: function () {
		this.icon.icon('destroy');
		this.label.remove();
		this.element.removeClass('ui-widget')
			.removeClass('ui-widget-header')
			.removeClass('ui-progressIndicator')
			.corner('destroy')
			.empty();
		this._originalContent.appendTo(this.element);
		$.Widget.prototype.destroy.apply(this, arguments);
	}
});