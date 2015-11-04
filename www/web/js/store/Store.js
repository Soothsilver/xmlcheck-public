/**
 * Manages loading and caching of remote data.
 */
asm.ui.Store = Base.extend({
	/**
	 * Initializes instance with supplied configuration.
	 * @tparam object config configuration properties:
	 * @arg @a request ID of request used to load data
	 * @arg @a arguments @optional request arguments
	 * @arg @a timeout @optional time until loaded data expires in miliseconds
	 *		(defaults to 5 minutes)
	 */
	constructor: function (config) {
		var defaults = {
			arguments: {},
			request: null,
			timeout: 5 * 60 * 1000
		};
		this.config = $.extend(defaults, config);

		this._initCache(this.config.timeout);
		this._requestLock = new asm.ui.Lock();
		this._revision = 0;
	},
	/**
	 * Transforms loaded data before it is stored.
	 * @tparam mixed data
	 * @treturn mixed unchanged data (override in descendants)
	 */
	_translate: function (data) {
		return data;
	},
	/**
	 * Compares new data with cached data.
	 * Does deep (recursive) comparation if @a data is object or array, simple
	 * equality check otherwise.
	 * @tparam mixed data
	 * @treturn true if @a data is equal to cached data
	 */
	_compare: function (data) {
		return (function compare (oldData, newData) {
			if (typeof newData != typeof oldData) {
				return false;
			}

			if (($.isPlainObject(oldData) && $.isPlainObject(newData))
					|| ($.isArray(oldData) && $.isArray(newData))) {
				var keys = $.extend(asm.ui.ObjectUtils.keys(oldData),
						asm.ui.ObjectUtils.keys(newData));
				for (var i in keys) {
					var key = keys[i];
					if (!compare(oldData[key], newData[key])) {
						return false;
					}
				}
				return true;
			} else {
				return (oldData == newData);
			}
		})(this._getCache(), data);
	},
	/**
	 * Refreshes store data from server.
	 * Lock is used to ensure that no requests are sent while request is already
	 * in progress. In that case supplied callback is put on hold and called when
	 * current request is completed.
	 * @warning Second callback argument (errors) is not passed to callbacks put
	 *		on hold while request is already in progress. However, those callbacks
	 *		will still receive null as first argument.
	 * @tparam function callback @optional will be called after store is refreshed
	 *		with following arguments:
	 *	@li refreshed store data (null if refresh failed)
	 *	@li errors (undefined if refresh succeeded) (unreliable, see method description)
	 *	@see get()
	 *	@see getRevision()
	 */
	refresh: function (callback) {

		callback = callback || $.noop;
		// Hack because of SubmissionDetails
		if (this.config.arguments.newId == 0) {
			callback([]);
			return;
		}

			var lockAcquired = this._requestLock.acquire(function () {
			callback(this._isCacheExpired() ? null : this._getCache());
		}, this);

		if (lockAcquired) {
			asm.ui.globals.coreCommunicator.request(this.config.request, this.config.arguments, $.proxy(function (data) {
				var translated = this._translate(data);
				if (!this._compare(translated)) {
					this._setCache(translated);
					++this._revision;
					this.trigger('store.change', { revision: this._revision });
				} else {
					this._refreshCacheTimestamp();
				}
				callback(translated);
			}, this), $.proxy(function (errors) {
				callback(null, errors);
			}, this), $.proxy(function () {
				this._requestLock.release();
			}, this));
		}
	},
	/**
	 * Gets stored data.
	 * Can be used synchronously or asynchronously:
	 * @code
	 * // synchronous call (data is returned directly)
	 * var storeData = store.get();
	 * // asynchronous call (data is passed to callback, refreshed if needed)
	 * store.get(function (storeData, refreshErrors) {
	 *		// ...
	 * });
	 * @endcode
	 * Both call types may be combined.
	 * @tparam function callback
	 * @treturn mixed currently stored data
	 */
	get: function (callback) {
		callback = callback || $.noop;
		
		var cachedData = this._getCache(),
			isExpired = this._isCacheExpired();

		// asynchronous (load and pass result to callback)
		if (isExpired) {
			this.refresh(callback);
		} else {
			callback(cachedData);
		}

		// synchronous (from cache, may be expired)
		return cachedData;
	},
	/**
	 * Checks whether stored data is expired.
	 * @treturn true if data is expired
	 */
	isExpired: function () {
		return this._isCacheExpired();
	},
	/**
	 * Gets current store revision number.
	 * Revision number is raised every time new data is received from server
	 * (meaning every time store is refreshed and received data differs from
	 * previously stored data). This can be used to prevent unnecessary work.
	 * @treturn int
	 */
	getRevision: function () {
		return this._revision;
	},
	/**
	 * Sets time until currently stored data expires.
	 * @note Works only on current data, not if store is refreshed in the meantime.
	 * @tparam int duration time in miliseconds
	 */
	expire: function (duration) {
		this._expireCache(duration);
	},
	/**
	 * Deletes stored data.
	 */
	empty: function () {
		this._clearCache();
	}
});
asm.ui.Store.implement(asm.ui.Cached);
asm.ui.Store.implement(asm.ui.Eventful);