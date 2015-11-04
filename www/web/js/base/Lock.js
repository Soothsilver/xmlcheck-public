/**
 * Helps prevent redundant parallel performing of single expensive task (such as
 * server request).
 *
 * Such task should have a dedicated instance of this class ("a lock"). Every
 * time anybody wants to perform the task, they first need to "acquire the lock",
 * which is possible only if nobody else has acquired it already. After the lock
 * is acquired by one entity, others are prevented from acquiring it and have an
 * option to supply callbacks to be called after the task is finished.
 */
asm.ui.Lock = Base.extend({
	/**
	 * Initializes instance with supplied configuration.
	 * @tparam object config
	 *
	 * No configuration options are supported at this time.
	 */
	constructor: function (config) {
		var defaults = {};
		this.config = $.extend(defaults, config);
		
		this._locked = false;
		this._queue = [];
	},
	/**
	 * Locks the instance.
	 * Creates empty queue of callbacks waiting for lock release.
	 */
	_lock: function () {
		this._queue = [];
		this._locked = true;
	},
	/**
	 * Unlocks the instance.
	 * Calls all callbacks from waiting queue.
	 */
	_unlock: function () {
		this._locked = false;

		for (var i in this._queue) {
			this._queue[i].callback.call(this._queue[i].scope);
		}
	},
	/**
	 * Adds callback to waiting queue.
	 * @tparam function callback
	 * @tparam mixed scope @optional @c callback scope
	 */
	_addToQueue: function (callback, scope) {
		this._queue.push({
			callback: callback,
			scope: scope
		});
	},
	/**
	 * Acquires lock or adds supplied callback to waiting queue.
	 * @note If the instance is currently locked and @a callback is supplied, it
	 *		will be called upon lock release <b>without acquiring the lock for itself</b>.
	 *		Do not use this for blocking of multiple access to single-access resource.
	 *		See class description for possible use case.
	 * @tparam function callback @optional to be called when lock is released
	 * @tparam mixed scope @optional callback scope
	 * @treturn bool true if lock has been successfully acquired
	 */
	acquire: function (callback, scope) {
		scope = scope || window;

		if (this.isLocked()) {
			if (callback) {
				this._addToQueue(callback, scope);
			}
			return false;
		}

		this._lock();
		return true;
	},
	/**
	 * Releases acquired lock.
	 */
	release: function () {
		this._unlock();
	},
	/**
	 * Checks whether instance is currently locked.
	 * @treturn bool true if instance is locked
	 */
	isLocked: function () {
		return this._locked;
	}
});