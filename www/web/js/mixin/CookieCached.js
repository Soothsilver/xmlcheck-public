/**
 * Modifies caching so that cached data is saved to cookie and retrieved on cache
 * initialization.
 */
asm.ui.CookieCached = asm.ui.Cached.extend({
	/**
	 * Initializes caching variables and loads cache data stored in cookie.
	 * @note Must be called before any other caching methods are used.
	 * @tparam int timeout @optional default time until cache expires after it
	 *		is refreshed by _setCache() (defaults to 0)
	 *	@tparam string cookieName name of cookie used for data storage (must be
	 *		unique so that the data is not overwritten by other classes)
	 */
	_initCache: function (timeout, cookieName) {
		this.base(timeout);

		$.extend(this._cached, {
			cookieName: cookieName
		});
		this._loadCache();
	},
	/**
	 * Puts supplied data to cache (and refreshes timeout).
	 *	@tparam mixed data
	 *	@tparam bool noRefresh @optional set to true not to refresh timeout
	 */
	_setCache: function (data, noRefresh) {
		this.base(data, noRefresh);
		this._saveCache();
	},
	/**
	 *	@copydoc Cached::_expireCache()
	 */
	_expireCache: function (timeout) {
		this.base(timeout);
		this._saveCache();
	},
	_clearCache: function () {
		this.base();
		this._saveCache();
	},
	/**
	 * Loads data from cookie and sets cache if data hasn't expired yet.
	 * @note Shouldn't be called directly except from descendant mixins (data is
	 *		loaded automatically when initCache() is called).
	 */
	_loadCache: function () {
		var fromCookie = $.cookie(this._cached.cookieName);
		if (fromCookie != null) {
			var o = $.secureEvalJSON(fromCookie),
				timeRemaining = (o.expires || 0) - asm.ui.TimeUtils.time();
			if (timeRemaining > 0) {
				this._setCache(o.data);
				this._expireCache(timeRemaining);
			} else {
				this._clearCache();
			}
		}
	},
	/**
	 * Saves cached data to cookie (or deletes cookie if cache is expired).
	 * @note Shouldn't be called directly except from descendant mixins.
	 */
	_saveCache: function () {
		if (!this._isCacheExpired()) {
			$.cookie(this._cached.cookieName, $.toJSON({
				data: this._cached.data,
				expires: this._cached.timestamp + this._cached.timeout
			}), {
				expires: Math.ceil(this._cached.timeout / 24 * 60 * 60 * 1000)
			});
		} else {
			$.cookie(this._cached.cookieName, null);
		}
	}
});