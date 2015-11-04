/**
 * Base error wrapper class.
 */
asm.ui.Error = Base.extend({
	/**
	 * Initializes instance with supplied message and error severity.
	 * @tparam string message error message
	 * @tparam int severity @optional (defaults to Error::ERROR)
	 */
	constructor: function (message, severity) {
		$.extend(this, {
			_message: asm.ui.StringUtils.ucfirst(message),
			_resolved: false,
			_severity: severity || asm.ui.Error.ERROR,
			_timestamp: asm.ui.TimeUtils.mysqlTimestamp()
		});
	},
	/**
	 * Gets severity.
	 * @treturn int one of predefined severity constants (Error::NOTICE, ...)
	 */
	getSeverity: function () {
		return this._severity;
	},
	/**
	 * Gets time of error occurence in @c "YYYY-MM-DD hh:mm:ss" format.
	 * @treturn string time of error creation
	 */
	getTimestamp: function () {
		return this._timestamp;
	},
	/**
	 * Gets class-specific additional error info (to be overriden in descendants).
	 * @treturn object additional error info
	 */
	getDetails: function () {
		return {};
	},
	/**
	 * Checks whether this error is marked as resolved.
	 * @treturn bool true if error is marked as resolved
	 */
	isResolved: function () {
		return this._resolved;
	},
	/**
	 * Marks error as resolved.
	 */
	stopResolving: function () {
		this._resolved = true;
	},
	/**
	 * Gets error message.
	 * @treturn string error message
	 */
	toString: function () {
		return this._message;
	}
}, {
	/** severity of notices (just some info) */
	NOTICE: 1,
	/** severity of warnings (should be reported in some way) */
	WARNING: 2,
	/** severity of errors (should be reported and handled) */
	ERROR: 3,
	/** severity of fatal errors (should be handled as exceptions) */
	FATAL: 4
});