/**
 * Adds ability to trigger events & bind callbacks to them @mixin.
 *
 * It enables developer to use loose coupling of classes (and saves many problems
 * in case of future code refactoring).
 */
asm.ui.Eventful = Base.extend({
	/**
	 * Initializes instance with this mixin's data (if it's not initialized already).
	 * @note Called automatically on first use of other methods, do not call explicitly.
	 */
	_initEventful: function () {
		this._eventful = this._eventful || {
			counters: {},
			events: {},
			parent: null
		};
	},
	/**
	 * Sets instance's "parent" for event propagation purposes.
	 * @tparam Eventful parent
	 */
	_setEventParent: function (parent) {
		this._initEventful();
		this._eventful.parent = parent;
	},
	/**
	 * Triggers event and propagates it to parent.
	 * @note Shouldn't be called directly except from descendant mixins.
	 * @tparam string name event name
	 * @tparam object params custom event parameters to be passed to callbacks
	 * @tparam Eventful source event source (this instance or its child in event hierarchy)
	 * @treturn mixed value returned by last bound callback or null
	 */
	_trigger: function (name, params, source) {
		this._initEventful();
		var ret = null,
			propagationStopped = false,
			event = {
				source: source,
				stopPropagation: function () {
					propagationStopped = true;
				}
			};

		if (this._eventful.events[name] != undefined) {
			for (var i in this._eventful.events[name]) {
				var data = this._eventful.events[name][i];
				ret = data.callback.call(data.scope || window, $.extend(data.params, params), event);
			}
		}
		
		if (!propagationStopped && (this._eventful.parent != null)) {
			this._eventful.parent._trigger(name, params, source);
		}
		
		return ret;
	},
	/**
	 * Binds callback to be called on specified event.
	 * @tparam string name event name
	 * @tparam object params additional event parameters (added to those supplied
	 *		on event trigger) [optional]
	 *	@tparam function callback called when the event is triggered
	 *	@tparam mixed scope @a callback will be called in this scope
	 *	@treturn int ID of this bond (can be used to unbind() @a callback later)
	 *	@see unbind()
	 *	@see trigger()
	 */
	bind: function (name, params, callback, scope) {
		this._initEventful();
		if (!$.isPlainObject(params)) {
			scope = callback;
			callback = params;
			params = {};
		}
		if (!$.isFunction(callback)) {
			return false;
		}
		if (this._eventful.events[name] == undefined) {
			this._eventful.events[name] = [];
			this._eventful.counters[name] = 0;
		}
		var bindId = this._eventful.counters[name]++;
		this._eventful.events[name].push({
			id: bindId,
			callback: callback,
			scope: scope,
			params: params
		});
		return bindId;
	},
	/**
	 * Unbinds specified callback (or all of them) from selected event.
	 * @tparam string name event name
	 * @tparam mixed id event-specific callback ID returned by bind() (int), or
	 *		null to unbind all callbacks for this event
	 *	@treturn bool true if some callbacks were removed, false otherwise
	 *	@see bind()
	 */
	unbind: function (name, id) {
		this._initEventful();
		if (this._eventful.events[name] == undefined) {
			return false;
		}
		
		if (id == undefined) {
			delete this._eventful.events[name];
			delete this._eventful.counters[name];
			return true;
		} else {
			var found = false;
			$.each(this._eventful.events[name], $.proxy(function (i, data) {
				if (data.id === id) {
					found = true;
					this._eventful.events[name].splice(i, 1);
					// break $.each()
					return false;
				}
			}, this));
			return found;
		}
	},
	/**
	 * Triggers event with supplied name.
	 * All callbacks bound to that event name will be called.
	 * @tparam string name event name
	 * @tparam object params event parameters (extend parameters supplied to bind())
	 * @treturn value returned by last bound callback
	 * @see bind()
	 */
	trigger: function (name, params) {
		return this._trigger(name, params, this);
	}
});