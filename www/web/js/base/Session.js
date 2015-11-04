/**
 * User session manager.
 */
asm.ui.Session = Base.extend({
	/**
	 * Initializes instance with supplied configuration properties and loads current
	 * user session data from cookie cache.
	 * @tparam object config configuration properties
	 * 
	 * Supported configuration properties:
	 * @arg @a defaults default values for user account properties (see source
	 *		code for default value)
	 * @arg @a interval session refresh interval in miliseconds (defaults to 1 min.)
	 * @arg @a namespace cookie name to use for session storage (defaults to 'session')
	 * @arg @a timeout session timeout in miliseconds (defaults to 5 min.)
	 */
	constructor: function (config) {
		var defaults = {
			defaults: {
				email: '',
				id: null,
				lastAccess: '[never]',
				privileges: {},
				realName: '',
				username: '[not logged in]'
			},
			interval: 60 * 1000,
			namespace: 'session',
			timeout: 5 * 60 * 1000
		};
		this.config = $.extend(defaults, config);

		this._updateTimeout = null;
		this._expireTimeout = null;

		this._initCache(this.config.timeout, this.config.namespace);
		if (this.isValid()) {
			this._setTimeouts();
		}
	},
	/**
	 * Clears update & expire timeouts.
	 * @see _setTimeouts()
	 */
	_clearTimeouts: function () {
		if (this._updateTimeout !== null) {
			window.clearTimeout(this._updateTimeout);
			this._updateTimeout = null;
		}
		if (this._expireTimeout !== null) {
			window.clearTimeout(this._expireTimeout);
			this._expireTimeout = null;
		}
	},
	/**
	 * Sets update & expire timeouts for triggering of appropriate events.
	 * <tt>requestUpdate</tt> event is triggered after session refresh interval,
	 * while <tt>expire</tt> event is triggered after session timeout.
	 */
	_setTimeouts: function () {
		if (this._updateTimeout || this._expireTimeout) {
			this._clearTimeouts();
		}

		this._updateTimeout = window.setTimeout($.proxy(function () {
			$('body').one('mousemove.session', $.proxy(function () {
				if (this.isValid()) {
					this.trigger('requestUpdate');
				}
			}, this));
		}, this), this.config.interval);
		this._expireTimeout = window.setTimeout($.proxy(function () {
			this.destroy();
			this.trigger('expire');
		}, this), this.config.timeout);
	},
	/**
	 * Initializes session with supplied user data and sets event timeouts.
	 * @warning Destroys current session if supplied user data is invalid.
	 * @tparam object data must contain at least @c id and @c username properties
	 *		(see source code of constructor for full list)
	 */
	init: function (data) {
		if (!data || (data.id === null) || (data.username == '')) {
			this.destroy();
			return;
		}

		this._setCache($.extend({}, this.config.defaults, data));

		this._setTimeouts();
	},
	/**
	 * Destroys current user session and event timeouts.
	 */
	destroy: function () {
		this._clearTimeouts();
		this._clearCache();
	},
	/**
	 * Checks whether this session is currently valid (initialized with valid data
	 * and not yet expired or destroyed).
	 */
	isValid: function () {
		return !this._isCacheExpired();
	},
	/**
	 * Gets user property.
	 * @tparam string propName property name
	 * @treturn mixed property value or null if undefined
	 */
	getProperty: function (propName) {
		if (!this.isValid()) {
			return null;
		}

		var properties = this._getCache();
		return (properties && (properties[propName] !== undefined)) ? properties[propName] : null;
	},
	/**
	 * Sets new value of current user's real name property.
	 */
	setRealName: function (value) {
		this._editCache({ realName: value });
	},
	/**
	 * Sets new value of current user's email property.
	 */
	setEmail: function (value) {
		this._editCache({ email: value });
	},
	/**
	 * Refreshes session timeouts if it is not yet expired.
	 * @tparam int timeout @optional time to session expiration (defaults to
	 *		@c timeout configuration property)
	 */
	refresh: function (timeout) {
		if (!this.isValid()) {
			return;
		}

		this._expireCache(timeout || this.config.timeout);
		this._setTimeouts();
	},
    sendEmailOnSubmissionRatedStudent: function (value) {
        this._editCache({ sendEmailOnSubmissionRatedStudent: value });
    },
    sendEmailOnSubmissionConfirmedTutor: function (value) {
        this._editCache({ sendEmailOnSubmissionConfirmedTutor: value });
    },
    sendEmailOnAssignmentAvailableStudent: function (value) {
        this._editCache({ sendEmailOnAssignmentAvailableStudent: value });
    },
});
asm.ui.Session.implement(asm.ui.Eventful);
asm.ui.Session.implement(asm.ui.CookieCached);