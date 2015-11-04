/**
 * Base for all panels with content (partially) loaded from server.
 * Automates loading of remote content as a part of content building process.
 */
asm.ui.DynamicContentPanel = asm.ui.ContentPanel.extend({
	/**
	 * @copydoc ContentPanel::ContentPanel()
	 *
	 * Additional configuration options:
	 * @arg @a autoRefresh time before content is automatically refreshed in
	 *		miliseconds (0 to turn auto-refresh off (default))
	 * @arg @a autoRefreshForce forces content redraw when performing automatic
	 *		refresh
	 * @arg @a stores array of @ref Store "stores" to be used as sources of remote
	 *		content
	 *
	 * @a loaderDelay default value is increased to 100ms.
	 */
	constructor: function (config) {
		var defaults = {
			autoRefresh: 0,
			autoRefresh: 0,
			autoRefreshForce: false,
			loaderDelay: 100,
			stores: []
		};
		this.base($.extend(defaults, config));

		this._storeRevisions = [];
		this._updateStoreRevisions();

		this._autoRefreshInterval = null;
		if (this.config.autoRefresh) {
			this._autoRefreshInterval = window.setInterval($.proxy(function () {
				this.refresh(this.config.autoRefreshForce);
			}, this), this.config.autoRefresh);
		}
	},
	/**
	 * Initializes built static content with loaded remote data.
	 */
	_initContent: function () {
	},
	/**
	 * Checks whether content of any used store has changed and updates internal
	 * store revision index.
	 */
	_updateStoreRevisions: function () {
		var storesChanged = false;
		for (var i in this.config.stores) {
			var newRevision = this.config.stores[i].getRevision();
			if (newRevision != this._storeRevisions[i]) {
				storesChanged = true;
			}
			this._storeRevisions[i] = newRevision;
		}
		return storesChanged;
	},
	/**
	 * Refreshes all used stores that are expired.
	 * @tparam function successCallback to be called if refresh was successful
	 * @tparam function failureCallback to be called if refresh failed
	 * @tparam function completionCallback to be called after refresh finishes
	 *		regardless of its success (called after success/failure callback)
	 */
	_refreshStores: function (successCallback, failureCallback, completionCallback) {
		this._setLoaderText(asm.lang.general.loadingData);
		var failed = false,
			refreshErrors = [],
			unfinished = this.config.stores.length,
			doneLoadingCallback = $.proxy(function () {
				if (refreshErrors.length) {
					this._triggerError(refreshErrors);
				}
				if (failed) {
					failureCallback();
				} else {
					successCallback();
				}
				completionCallback();
			}, this),
			loadCallback = function () {
				--unfinished;
				if (unfinished == 0) {
					doneLoadingCallback();
				}
			};

		if (!this.config.stores.length) {
			++unfinished;
			loadCallback();
		} else {
			for (var i in this.config.stores) {
				var store = this.config.stores[i];
				if (store.isExpired()) {
					store.refresh($.proxy(function (data, errors) {
						if (data === null) {
							failed = true;
						}
						if (errors && errors.length) {
							$.merge(refreshErrors, errors);
						}
						loadCallback();
					}, this));
				} else {
					loadCallback();
				}
			}
		}
	},
	/**
	 * Loads remote data from stores and initializes content with it if loading
	 * was successful.
	 * @tparam function callback to be called on finish
	 */
	_loadAndInitContent: function (callback) {
		this._refreshStores($.proxy(function () {
			if (this._loading && this._updateStoreRevisions()) {

                this._setLoaderText(asm.lang.general.initializing);
                this._initContent();
                this.trigger('panel.init');

			}
		}, this), $.proxy(function () {
			this._triggerError(new asm.ui.Error('Error while loading data from server. Application may not function properly.', asm.ui.Error.ERROR));
		}, this), callback);
	},
	/**
	 * @copybrief ContentPanel::_showContent()
	 *
	 * @tparam function callback to be called after content is shown
	 */
	_showContent: function (callback) {
		this._startLoading();
		this._buildContentCached();
		this._loadAndInitContent($.proxy(function () {
			callback();
		}, this));
	},
	/**
	 * @copybrief ContentPanel::_showAndAdjustContent()
	 *
	 * Method is overriden to support asynchronous loading of remote data.
	 */
	_showAndAdjustContent: function () {
		this._showContent($.proxy(function () {
			this.trigger('panel.show');
			this.adjust(this._params, true);
			this._finishLoading();
		}, this));
	},
	destroy: function () {
		if (this._autoRefreshInterval !== null) {
			window.clearInterval(this._autoRefreshInterval);
		}
		this.base();
	},
	/**
	 * Refreshes expired store and updates panel content if any store data changed.
	 * @tparam bool force true to force refresh of all used stores (not just expired)
	 * @treturn DynamicContentPanel self
	 */
	refresh: function (force) {
		if (force) {
			for (var i in this.config.stores) {
				this.config.stores[i].expire();
			}
		}
		if (this._shown) {
			this._startLoading();
			this._loadAndInitContent($.proxy(function () {
				this.adjust(this._params, true);
				this._finishLoading();
				this.trigger('panel.refresh');
			}, this));
		}
		return this;
	}
});
