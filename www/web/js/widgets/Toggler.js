/**
 * Creates button with set of 'states'.
 * On every click, button state is changed (incremented in cycle) and 'statechange'
 * event is triggered.
 */
$.widget('ui.toggler', {
	options: {
		/** button states (sets of button widget options) */
		states: [
			{ icons: { primary: 'ui-icon-radio-on' } },
			{ icons: { primary: 'ui-icon-radio-off' } }
		],
		/** index of current state */
		state: 0
	},
	_create: function () {
		this.element.addClass('ui-toggler')
			.button()
			.bind('click.toggler', $.proxy(function (event) {
				event.stopPropagation();
				this.toggle();
			}, this));

		this._setState();
	},
	_setOption: function (key, value) {
		switch (key) {
			case 'state':
				if ((value < 0) || (value >= this.options.states.length)) {
					value = 0;
				}
				break;
		}
		$.Widget.prototype._setOption.apply(this, arguments);
		switch (key) {
			case 'states':
				if (this.options.state >= value.length) {
					this.option('state', 0);
				}
				this._setState();
				break;
			case 'state':
				this._setState();
				break;
		}
	},
	_setState: function () {
		this.element.button(this.options.states[this.options.state]);
		this._trigger('statechange');
	},
	destroy: function () {
		this.element.removeClass('ui-toggler')
			.unbind('click.toggler')
			.button('destroy');
	},
	/**
	 * Change state to next one.
	 */
	toggle: function () {
		this.option('state', this.options.state + 1);
	}
});