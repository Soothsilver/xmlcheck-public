/** 
 * Adds convenience methods to be used for caching instance data @mixin.
 */
asm.ui.Cached = Base.extend({
	/**
	 * Initializes caching variables.
	 * @note Must be called before any other caching methods are used.
	 * @tparam int timeout @optional default time until cache expires after it
	 *		is refreshed by _setCache() (defaults to 0)
	 */
	_initCache: function (timeout) {
		this._cached = {
			data: null,
			timeout: timeout || 0,
			timestamp: 0
		};
	},
	/**
	 * Checks whether cache is expired.
	 * @treturn bool true if cache is expired
	 */
	_isCacheExpired: function () {
		return (asm.ui.TimeUtils.time() > this._cached.timestamp + this._cached.timeout);
	},
	/**
	 * Gets data from cache.
	 * @tparam bool freshOnly @optional return null if cache is expired
	 * @treturn mixed data from cache (or null if @a freshOnly is set to true and
	 *		cache is expired)
	 */
	_getCache: function (freshOnly) {
		if (freshOnly && this._isCacheExpired()) {
			return null;
		}
		
		return this._cached.data;
	},
	/**
	 * Puts supplied data to cache (and refreshes timeout).
	 *	@tparam mixed data
	 *	@tparam bool noRefresh @optional set to true not to refresh timeout
	 */
	_setCache: function (data, noRefresh) {
		if (!noRefresh) {
			this._refreshCacheTimestamp();
		}
		this._cached.data = data;
	},
	/**
	 * Update cache with supplied data.
	 * @note Doesn't refresh cache timeout (use _expireCache() for that).
	 * @tparam mixed data
	 */
	_editCache: function (data) {
		this._setCache($.extend({}, this._getCache(), data), true);
	},
	/**
	 * Sets time until currently cached data expires.
	 * @note Default timeout will still be used when _setCache() is called - this
	 *		override is only for current cache contents.
	 * @tparam int timeout time until currently cached data expires
	 */
	_expireCache: function (timeout) {
		if (timeout) {
			this._cached.timestamp = asm.ui.TimeUtils.time() + timeout - this._cached.timeout;
		} else {
			this._cached.timestamp = 0;
		}
	},
	/**
	 * Refreshes cache timestamp (default timeout starts over).
	 */
	_refreshCacheTimestamp: function () {
		this._cached.timestamp = asm.ui.TimeUtils.time();
	},
	/**
	 * Clears cache and marks it as expired.
	 */
	_clearCache: function () {
		$.extend(this._cached, {
			data: null,
			timestamp: 0
		});
	}
});