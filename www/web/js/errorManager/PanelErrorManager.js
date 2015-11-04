/**
 * @copybrief ErrorManager
 * 
 * Displays errors in designated location, each in panel widget.
 * Allows automatic hiding of errors after specific timeout. Each error panel
 * can be hidden manually by user or 'pinned' to prevent automatic hiding.
 */
asm.ui.PanelErrorManager = asm.ui.ErrorManager.extend({
	/**
	 * @copybrief ErrorManager::ErrorManager()
	 * @tparam object config
	 * @arg @a hideEffect @optional hide animation (none by default)
	 * @arg @a hideEffectOptions @optional effect-specific options
	 * @arg @a showEffect @optional show animation (none by default)
	 * @arg @a showEffectOptions @optional effect-specific options
	 * @arg @a target container to which error message panels will be rendered
	 * @arg @a timeout @optional time after which shown errors are automatically
	 *		hidden (defaults to 0 == never)
	 */
	constructor: function (config) {
		var defaults = {
			hideEffect: undefined,
			hideEffectOptions: {},
			showEffect: undefined,
			showEffectOptions: {},
			target: $(),
			timeout: 0
		};
		this.base($.extend(defaults, config));

		this._panels = {};
		this._pinned = {};
		this._expired = {};
	},
	/**
	 * Gets panel highlight for supplied error severity.
	 * @tparam int severity
	 * @treturn string highlight mode (accepted as @c highlight option of panel
	 *		widget config)
	 */
	_selectHighlight: function (severity) {
		switch (severity) {
			case asm.ui.Error.FATAL:
			case asm.ui.Error.ERROR:
				return 'error';
			case asm.ui.Error.WARNING:
				return 'highlight';
			default:
				return 'none';
		}
	},
	/**
	 * Gets panel icon for supplied error severity.
	 * @tparam int severity
	 * @treturn string icon name
	 */
	_selectIcon: function (severity) {
		switch (severity) {
			case asm.ui.Error.FATAL:
				return 'alert';
			case asm.ui.Error.ERROR:
			case asm.ui.Error.WARNING:
				return 'notice';
			default:
				return 'info';
		}
	},
	/**
	 * Creates and shows error panel based on supplied error.
	 * @tparam mixed error
	 * @treturn jQueryEl panel element
	 */
	_createErrorPanel: function (error) {
		var message = error.toString(),
			severity = error.getSeverity(),
			panel = $('<div></div>')
				.append(message)
				.appendTo(this.config.target)
				.panel({
					highlight: this._selectHighlight(severity),
					icon: this._selectIcon(severity)
				})
				.panel('hide');

		var pin = $('<div></div>').addClass('actionIcon')
			.attr('title', asm.lang.errors.pin)
			.prependTo(panel)
			.icon({ type: 'pin-s' })
			.click($.proxy(function () {
				var wasPinned = this._isPinned(error);
				this[wasPinned ? '_unpin' : '_pin'].call(this, error);
				pin.attr('title', wasPinned ? asm.lang.errors.pin : asm.lang.errors.unpin)
					.icon('option', 'type', wasPinned ? 'pin-s' : 'pin-w');
			}, this));

		$('<div></div>').addClass('actionIcon')
			.attr('title', asm.lang.errors.hideError)
			.prependTo(panel)
			.icon({ type: 'close' })
			.click($.proxy(function () {
				this._hide(error);
			}, this));

		return panel;
	},
	/**
	 * Checks whether supplied error is marked as expired.
	 * @tparam mixed error
	 * @treturn bool true if @a error is marked as expired
	 * @see _expire()
	 */
	_isExpired: function (error) {
		return this._expired[error.toString()] || false;
	},
	/**
	 * Handles error expiration.
	 * Hides error if it's not pinned, marks it as expired otherwise.
	 * @tparam mixed error
	 */
	_expire: function (error) {
		if (this._isPinned(error)) {
			this._expired[error.toString()] = true;
		} else {
			this._hide(error);
		}
	},
	/**
	 * Checks whether supplied error is 'pinned'.
	 * @tparam mixed error
	 * @treturn bool true if @a error is pinned
	 * @see _pin()
	 */
	_isPinned: function (error) {
		return this._pinned[error.toString()] || false;
	},
	/**
	 * 'Pins' supplied error (prevents it from being automatically hidden after
	 * preset timeout).
	 * @tparam mixed error
	 * @see _unpin()
	 */
	_pin: function (error) {
		this._pinned[error.toString()] = true;
	},
	/**
	 * 'Unpins' supplied error (see _pin()).
	 * @tparam mixed error
	 * @see _pin()
	 */
	_unpin: function (error) {
		var index = error.toString();
		delete this._pinned[index];
		if (this._isExpired(error)) {
			this._hide(error);
		}
	},
	/**
	 * Shows supplied error in appropriately styled panel.
	 * @tparam mixed error
	 * @see _hide()
	 */
	_show: function (error) {
		var message = error.toString(),
			panel = this._createErrorPanel(error);

		this._panels[message] = panel;

		if (this.config.timeout) {
			window.setTimeout(asm.ui.Utils.proxy(function (error) {
				this._expire(error);
			}, this, error), this.config.timeout * 1000);
		}
		
		panel.panel('show', this.config.showEffect, this.config.showEffectOptions);
	},
	/**
	 * Hides supplied error.
	 * @tparam mixed error
	 * @see _show()
	 */
	_hide: function (error) {
		var index = error.toString(),
			panel = this._panels[index];

		panel.panel('hide', this.config.hideEffect, this.config.hideEffectOptions, function () {
			panel.panel('destroy')
				.remove();
		});

		if (this._pinned[index]) {
			delete this._pinned[index];
		}
		if (this._expired[index]) {
			delete this._expired[index];
		}
			
		this.base(error);
	},
	/**
	 * Sets new target for error panels to be rendered in.
	 * @warning Doesn't move already rendered panels.
	 * @tparam jQueryEl target
	 */
	setTarget: function (target) {
		this.config.target = target;
	}
});