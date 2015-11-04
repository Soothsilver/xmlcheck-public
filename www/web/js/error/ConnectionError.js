/**
 * %Error related to failure of request to server.
 */
asm.ui.ConnectionError = asm.ui.Error.extend({
	/**
	 * Initializes error with supplied message & data related to failed request.
	 * @note Request-related data will be available through getDetails() method,
	 *		not added to error message.
	 * @tparam string message error message
	 * @tparam string request failed request ID
	 * @tparam object args @optional failed request arguments
	 * @tparam int severity @optional (passed to parent constructor)
	 */
	constructor: function (message, request, args, severity) {
		this.base(message, severity);

		$.extend(this, {
			_request: request,
			_arguments: args || {}
		});
	},
	/**
	 * Gets additional error data.
	 * @treturn object object with following properties in addition to those provided
	 *		by parents:
	 * @li @c request failed request ID
	 * @li @c arguments failed request arguments
	 */
	getDetails: function () {
		return $.extend(this.base(), {
			request: this._request,
			arguments: this._arguments
		});
	}
});