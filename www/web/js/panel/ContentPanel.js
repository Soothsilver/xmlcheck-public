/**
 * Base for all panels with own content (as opposed to Container).
 * Contains means for indicating progress of content building and for content
 * caching. How the content is generated must be specified in descendants in
 * override of the _buildContent() method.
 */
asm.ui.ContentPanel = asm.ui.Panel.extend({
	/**
	 * @copydoc Panel::Panel()
	 *
	 * Additional configuration options:
	 * @arg @a loaderDelay (int) base content building time after which progress indicator
	 *		is shown in miliseconds (defaults to 10). Not showing the indicator for
	 *		short build times removes situations where the indicator would just pop
	 *		up and disappear again immediately.
	 * @arg @a noCache (bool) true to turn off content caching (with content caching on
	 *		content is built only once, otherwise it's built every time panel is shown)
	 * @arg @a showLoader (bool) false to disable showing of progress indicator
	 *		while building content (loading overlay is shown regardless of this option)
	 */
	constructor: function (config) {
		var defaults = {
			loaderDelay: 10,
			noCache: false,
			showLoader: true
		};
		this.base($.extend(defaults, config));

		this._cachedTarget = $();
		this._loading = false;
		this._loader = $();
		this._loaderTimeout = null;
	},
	/**
	 * Builds static panel content.
	 * If content caching is on (see ContentPanel()), this method is called just
	 * once before the panel is first shown. Otherwise it is called before every
	 * showing.
	 */
	_buildContent: function () {
		// to be overridden - create initial page content (static)
	},
	/**
	 * Sets build progress indicator label.
	 * @note Loader may or may not be shown, depending on panel
	 *		@ref ContentPanel::ContentPanel() "configuration".
	 */
	_setLoaderText: function (text) {
		this._loader.progressIndicator('option', 'text', text);
	},
	/**
	 * Creates and shows overlay to be shown over panel while content is being built.
	 * Also creates build progress indicator on the overlay to be shown after
	 * time set in panel @ref ContentPanel::ContentPanel() "configuration".
	 *	@treturn bool false if content is already being built, true otherwise
	 */
	_startLoading: function () {
		if (this._loading) {
			return false;
		}
		this._loading = true;
		
		this._loader = $('<div></div>').progressIndicator({
				text: ''
			})
			.toggle(this.config.showLoader);

		this.config.target.overlay({
			content: this._loader
		});

		this._loaderTimeout = window.setTimeout($.proxy(function () {
			this._loaderTimeout = null;
			this.config.target.overlay('show', 0);
		}, this), this.config.loaderDelay);

		return true;
	},
	/**
	 * Builds content or retrieves it from cache.
	 */
	_buildContentCached: function () {
		if (this._cachedTarget.length) {
			this._cachedTarget.insertAfter(this.config.target);
			this.config.target.remove();
			this.config.target = this._cachedTarget;
		} else {
			this._setLoaderText('Building content');
			this._buildContent();
		}
	},
	/**
	 * Hides and destroys build overlay.
	 * @treturn bool false if content is not currently being built, true otherwise
	 */
	_finishLoading: function () {
		if (!this._loading) {
			return false;
		}

		var destroyOverlay = $.proxy(function () {
			if (this._loader.length) {
				this._loader.progressIndicator('destroy');
			}
			this.config.target.overlay('destroy');
			
			this._loading = false;
		}, this);

		if (this._loaderTimeout === null) {
			this.config.target.overlay('hide', destroyOverlay);
		} else {
			window.clearTimeout(this._loaderTimeout);
			this._loaderTimeout = null;
			destroyOverlay();
		}

		return true;
	},
	/**
	 * @copybrief Panel::_showContent()
	 *
	 * Calls _buildContentCached().
	 */
	_showContent: function () {
		this._buildContentCached();
	},
	/**
	 * @copybrief Panel::_showAndAdjustContent()
	 *
	 * Build overlay is added over the content while it is being (re)built with
	 * build progress indicator on top.
	 */
	_showAndAdjustContent: function () {
		this._startLoading();
		this._showContent();
		this.trigger('panel.show');
		this.adjust(this._params, true);
		this._finishLoading();
	},
	/**
	 * @copybrief Panel::_adjustContent()
	 *
	 * Sets build progress indicator label to 'Adjusting'.
	 */
	_adjustContent: function () {
		this._setLoaderText(asm.lang.general.adjusting);
	},
	/**
	 * Hides content.
	 * @tparam bool noCache true to destroy content instead of caching it
	 */
	_hideContentCached: function (noCache) {
		if (noCache) {
			this._cachedTarget.remove();
			this._cachedTarget = $();
			this.config.target.empty();
		} else {
			this.config.target.find('*').mouseout();
			this._cachedTarget = this.config.target;
			this.config.target = $('<span></span>').addClass('ui-placeholder')
				.insertAfter(this.config.target);
			this._cachedTarget.detach();
		}
	},
	/**
	 * @copybrief Panel::_hideContent()
	 *
	 * Calls _hideContentCached() with @c noCache configuration option as argument.
	 */
	_hideContent: function () {
		this._finishLoading();
		this._hideContentCached(this.config.noCache);
	}
});
asm.ui.ContentPanel.implement(asm.ui.Builder);