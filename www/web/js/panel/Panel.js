/**
 * Base for all classes for content display management.
 *
 * Display management follows a fairly simple model. All Panel descendants have
 * three main display methods: show(), adjust(), and hide(), which manage main
 * show/hide/change parameters logic. These methods should never be overriden
 * in descendants. Instead, corresponding protected methods _showContent(),
 * _adjustContent(), _showAndAdjustContent(), and _hideContent(), which are called
 * only when appropriate (i.e. _showContent() is called from show() only if the
 * content isn't already shown), should be overriden. Display parameters are
 * accessible from these methods as @c _params instance member.
 *
 * Every panel has DOM node target it is rendered to. It can be supplied
 * to panel constructor (recommended), or set using move() method. Latter means
 * additional overhead, because if the panel is currently shown, it must be hidden
 * and shown again in new location.
 */
asm.ui.Panel = Base.extend({
	/**
	 * Initializes instance using supplied configuration.
	 * Supports following configuration options:
	 * @arg @a classes array of classes to apply to target element
	 * @arg @a params (object) default display parameters
	 * @arg @a target (jQueryEl) rendering target (container for panel content)
	 *
	 * @tparam object config configuration options
	 */
	constructor: function (config) {
		var defaults = {
			classes: [],
			params: [],
			target: $()
		};
		this.config = $.extend(defaults, config);

		this._parent = null;
		this._params = this.config.params;
		this._shown = false;
		this._adjusting = false;
		this._destroyed = false;

		if (this.config.target) {
			this._initTarget();
		}
	},
	/**
	 * Initializes panel target to be used as container for panel content.
	 * Empties target of original content.
	 * @warning Original panel content will be removed from DOM permanently.
	 */
	_initTarget: function () {
		this.config.target.empty();
	},
	/**
	 * Adds custom classes (supplied to constructor) to target.
	 */
	_addCustomClasses: function () {
		for (var i in this.config.classes) {
			this.config.target.addClass(this.config.classes[i]);
		}
	},
	/**
	 * Removes custom classes (supplied to constructor) from target.
	 */
	_removeCustomClasses: function () {
		for (var i in this.config.classes) {
			this.config.target.removeClass(this.config.classes[i]);
		}
	},
	/**
	 * (Creates and) shows panel content.
	 */
	_showContent: function () {
		// override in descendants
	},
	/**
	 * Adjusts panel content to reflect current display parameters.
	 */
	_adjustContent: function () {
		// override in descendants
	},
	/**
	 * Hides panel content.
	 */
	_hideContent: function () {
		// override in descendants
	},
	/**
	 * Moves panel content to new container.
	 */
	_moveContent: function () {
		// override in descendants
	},
	/**
	 * (Creates and) shows content and adjusts it to reflect current display parameters.
	 */
	_showAndAdjustContent: function () {
		this._showContent();
		this.trigger('panel.show');
		this.adjust(this._params, true);
	},
	/**
	 * Requests adjustment of display parameters by triggering
	 * <tt>panel.adjustRequest</tt> event.
	 * This method should be called by descendants when they are given invalid
	 * display parameters.
	 * @tparam array params suggested valid parameters replacement (<b>these
	 *		parameters must be valid, so as not to trigger same event again!</b>)
	 * @note Triggered event should be handled, so that the panels are not left
	 *		in inconsistent state.
	 */
	_requestAdjust: function (params) {
		if (params == undefined) {
			params = [];
		} else if (!$.isArray(params)) {
			params = [params];
		}
		this.trigger('panel.adjustRequest', { params: params });
	},
	/**
	 * Reports error by triggering <tt>panel.error</tt> event.
	 * @tparam mixed error either a single error (Error) or array of errors
	 */
	_triggerError: function (error) {
		if (!$.isArray(error)) {
			error = [error];
		}
		for (var i in error) {
			this.trigger('panel.error', { error: error[i] } );
		}
	},
	/**
	 * Destroys all used widgets, resources, etc.
	 * @note Should be called before panel instance is thrown away to prevent memory
	 *		leaks.
	 * @note panel can no longer be used after it is destroyed.
	 */
	destroy: function () {
		this._destroyed = true;
		return this;
	},
	/**
	 * Sets element parent for event propagation purposes.
	 * Should be used in container panels to propagate events from children up the
	 * container hierarchy. That way all unhandled events can be caught at the root
	 * panel.
	 * @tparam Panel parent
	 */
	setParent: function (parent) {
		this._parent = parent;
		this._setEventParent(this._parent);
	},
	/**
	 * Changes Panel instance configuration.
	 * @note Should be called only when really necessary, because it may lead
	 *		to rebuilding of a lot of content.
	 *	@tparam object config see Panel()
	 */
	changeConfig: function (config) {
		var isShown = this._shown;
		$.extend(this.config, config);
		if (isShown) {
			this.show(this._params, true);
		}
	},
	/**
	 * Shows panel content and adjusts it to reflect supplied display parameters.
	 * @warning Do not override this method, override _showAndAdjustContent() instead.
	 * @tparam array params @optional display parameters (see adjust())
	 * @tparam bool forceRender @optional true to hide content and then show it if
	 *		it is being shown already (otherwise it is just @ref adjust() adjusted)
	 *	@treturn Panel self
	 */
	show: function (params, forceRender) {
		if (this._shown && forceRender) {
			this.hide();
		}
		
		if (!this._shown) {
			this.adjust(params, false); // This will only change _params, won't actually do any adjusting
			this._shown = true;
			this._addCustomClasses();
			this._showAndAdjustContent();
		} else {
			this.adjust(params, false)
		}
		return this;
	},
	/**
	 * Adjusts panel content to reflect supplied display parameters.
	 * @warning Do not override this method, override _adjustContent() instead.
	 * @tparam array params @optional display parameters (default parameters from
	 *		instance configuration are used if this argument is not supplied)
	 * @tparam bool force @optional true to re-adjust content event if parameters
	 *		are unchanged
	 *	@treturn Panel self
	 */
	adjust: function (params, force) {
		var oldParams = this._params;
		this._params = params || this.config.params;
		if (this._shown && (force || !asm.ui.ArrayUtils.compare(this._params, oldParams))) {
			this._adjusting = true;
			this._adjustContent();
			this.trigger('panel.adjust');
			this._adjusting = false;
		}
		return this;
	},
	/**
	 * Hides panel content.
	 * @warning Do not override this method, override _hideContent() instead.
	 *	@treturn Panel self
	 */
	hide: function () {
		if (this._shown) {
			this._hideContent();
			this._removeCustomClasses();
			this.trigger('panel.hide');
			this._shown = false;
		}
		return this;
	},
	/**
	 * Initializes supplied target as new container for panel content and moves
	 * panel content there.
	 * @note Forces content to be hidden and shown again.
	 * @warning Do not override this method, override _moveContent() instead.
	 *	@treturn Panel self
	 */
	move: function (target) {
		var wasShown = this._shown;
		this.hide();
		var oldTarget = this.config.target || $();
		this.config.target = target;
		this._initTarget();
		this._moveContent(oldTarget);
		if (wasShown) {
			this.show();
		}
		return this;
	}
});
asm.ui.Panel.implement(asm.ui.Eventful);