/**
 * Manager of application configuration.
 * Configuration is stored in cookie and retrieved on class instantialization.
 *
 * Triggers events on configuration property changes.
 *
 * @warning Do not create more than one instance of this class at a time.
 */
asm.ui.Config = Base.extend({
	/**
	 * Initializes instance with supplied configuration.
	 * @tparam object config configuration properties
	 *
	 * Supported configuration properties:
	 * @arg @a defaults default property values
	 * @arg @a expires cookie expiration property
	 */
	constructor: function (config) {
		var defaults = {
			defaults: {},
			expires: 0
		};
		this.config = $.extend(true, defaults, config);

		var fromCookie = $.cookie('config');
		this.data = $.extend({}, this.config.defaults,
				((fromCookie != null) ? $.secureEvalJSON(fromCookie) : {}));
	},
	/**
	 * Gets configuration property value.
	 * See set() for sample use.
	 * @tparam string [...] key sequence (one key for each configuraton level)
	 * @treturn mixed configuration property belonging to supplied key sequence
	 *		or null if supplied sequence doesn't exist in configuration
	 *	@see set()
	 */
	get: function () {
		var data = this.data;
		$.each(arguments, function (i, key) {
			if (data[key] == undefined) {
				data = null;
				return false; // break $.each()
			} else {
				data = data[key];
			}
		});
		return data;
	},
	/**
	 * Sets configuration property value.
	 * Sample use:
	 * @code
	 * var config = new asm.ui.Config({
	 *		defaults: {
	 *			theme: 'ui-lightness',
	 *			connection: {
	 *				type: 'JSON',
	 *				remoteAddr: 'somewhere.far.away'
	 *			}
	 *		}
	 * });
	 * var out = [];
	 * out.push(config.get('connection', 'type'));
	 * out.push(config.get('connection'));
	 * config.set(true, 'connection', 'secure');
	 * out.push(config.get('connection'));
	 * out
	 * @endcode
	 * yields
	 * @code
	 * ['JSON', {
	 *		type: 'JSON',
	 *		remoteAddr: 'somewhere.far.away'
	 * }, {
	 *		type: 'JSON',
	 *		remoteAddr: 'somewhere.far.away',
	 *		secure: true
	 * }]
	 * @endcode
	 * @tparam string value property value
	 * @tparam string [...] key sequence
	 * @see get()
	 */
	set: function (value) { // VALUE AS FIRST ARGUMENT, then key sequence
		var args = $.makeArray(arguments),
			value = args.shift(),
			key = args.pop(),
			data = this.data;
		$.each(args, function (i, k) {
			if (data[k] == undefined) {
				data[k] = {};
			}
			data = data[k];
		});
		
		var changed = (value !== data[key]);
		data[key] = value;
		if (changed) {
			args.push(key);
			this.triggerChange(args);
		}

		$.cookie('config', $.toJSON(this.data), { expires: this.config.expires });

		return value;
	},
	/**
	 * Creates composite event name for configuration property change event.
	 * @tparam mixed keySequence single key (string) or key sequence (array)
	 * @treturn string change event name consisting of property key sequence joined
	 *		by dots with appended @c ".change" (e.g. @c "connection.type.change")
	 */
	_createChangeEventType: function (keySequence) {
		if (!$.isArray(keySequence)) {
			keySequence = [keySequence];
		} else {
			keySequence = keySequence.slice();
		}
		keySequence.push('change');

		return keySequence.join('.');
	},
	/**
	 * Binds callback to configuration property change event.
	 * @tparam mixed keySequence single key (string) or key sequence (array)
	 * @tparam object params additional event parameters passed to @a callback
	 * @tparam function callback
	 * @tparam mixed scope @optional @a callback scope
	 * @treturn int bind ID (see Eventful::bind())
	 * @see unbindChange()
	 * @see triggerChange()
	 */
	bindChange: function (keySequence, params, callback, scope) {
		var shiftArgs = !$.isPlainObject(params),
			userCallback = shiftArgs ? params : callback;
		return this.bind(this._createChangeEventType(keySequence),
				shiftArgs ? {} : params, function (params) {
			return userCallback.call(this, params.value);
		}, shiftArgs ? callback : scope);
	},
	/**
	 * Unbinds callback from configuration property change event.
	 * @tparam mixed keySequence (see bindChange())
	 * @tparam int id bind ID (see Eventful::unbind())
	 * @see bindChange()
	 */
	unbindChange: function (keySequence, id) {
		return this.unbind(this._createChangeEventType(keySequence), id);
	},
	/**
	 * Triggers configuration property change events.
	 * Triggers two events: generic @c change event and property-specific
	 * <tt>&lt;key.sequence&gt;.change</tt> event. First event is triggered with
	 * additional @c keySeq parameter containing the key sequence array.
	 * @tparam mixed keySequence single key (string) or key sequence (array)
	 * @treturn mixed value returned by last callback bound to property-specific
	 *		change event.
	 */
	triggerChange: function (keySequence) {
		if (!$.isArray(keySequence)) {
			keySequence = [keySequence];
		}
		var value = this.get.apply(this, keySequence);
		this.trigger('change', {
			keySeq: keySequence,
			value: value
		});
		return this.trigger(this._createChangeEventType(keySequence), { value: value });
	}
});
asm.ui.Config.implement(asm.ui.Eventful);