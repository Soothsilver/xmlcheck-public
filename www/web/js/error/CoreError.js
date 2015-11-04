/**
 * %Error received from core in response to core request.
 */
asm.ui.CoreError = asm.ui.ConnectionError.extend({
	/**
	 * Initializes instance with supplied core error info & request-related info.
	 * @note Error message will be created from core error cause, effect, and details,
	 *		while error code and request-related info will be available through
	 *		getDetails() method.
	 *	@note Error severity is based on supplied core error code.
	 *	@tparam object o object with following properties:
	 *	@arg @a level (int) error level
	 *	@arg @a cause (string) error cause (what went wrong)
	 *	@arg @a effect @optional (string) error effect (which high-level action
	 *		could not be completed)
	 *	@arg @a details @optional (string) additional error info
	 * @tparam string request failed request ID
	 * @tparam object args @optional failed request arguments
	 */
	constructor: function (o, request, args) {
		var message = o.cause + (o.details ? '\n(' + o.details + ')' : '');
		if (o.effect) {
			message = o.effect + ': ' + message;
		}

		var severity;
		$.each(asm.ui.CoreError.tresholds, function (i, treshold) {
			if (o.level <= treshold[0]) {
				severity = treshold[1];
			} else {
				// break $.each()
				return false;
			}
		});

		this.base(message, request, args, severity);
		
		$.extend(this, {
			_code: o.code
		});
	},
	/**
	 * Gets additional error data.
	 * @treturn object object with @c code property (core error code) in addition
	 *		to those provided	by parents.
	 */
	getDetails: function () {
		return $.extend(this.base(), {
			code: this._code
		});
	}
}, {
	/** code of 'low privileges' core error */
	LOW_PRIVILEGES:	1,
	/** code of upload-related core error */
	UPLOAD:				11,
	/** code tresholds (base for error severity determination) */
	tresholds: [
		[100, asm.ui.Error.FATAL],
		[50, asm.ui.Error.ERROR],
		[25, asm.ui.Error.WARNING],
		[10, asm.ui.Error.NOTICE]
	]
});