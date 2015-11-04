/**
 * Base for core communicator classes.
 * Implements just the common communication management. The communication itself
 * must be implemented in descendants (_sendRequest() and _parseResponse() methods).
 */
asm.ui.CoreCommunicator = Base.extend({
	/**
	 * Sends request to core.
	 * @tparam object data request data (including request name)
	 * @tparam function successCallback @optional to be called on request success
	 * @tparam function failureCallback @optional to be called on request failure
	 * @see _parseResponse()
	 */
	_sendRequest: function (data, successCallback, failureCallback) {
		// needs override
	},
	/**
	 * Parses received response to sent core request.
	 * @tparam string response
	 * @see _sendRequest()
	 */
	_parseResponse: function (response) {
		// needs override
	},
	/**
	 * Sends request to core.
	 * @tparam string request request name
	 * @tparam object data @optional request arguments (may be left out even if
	 *		following arguments are supplied)
	 * @tparam function successCallback @optional to be called on request success
	 *		with parsed response data as first argument
	 *	@tparam function failureCallback @optional to be called on request failure
	 *		with array of errors as first argument
	 *	@tparam function callback @optional to be called regardless of request success
	 *		with array of errors as first argument
	 */
	request: function (request, data, successCallback, failureCallback, callback) {
		if ($.isFunction(data)) {
            callback = failureCallback;
			failureCallback = successCallback;
			successCallback = data;
			data = {};
		}

		var succeed = $.isFunction(successCallback) ? successCallback : $.noop,
			fail = $.isFunction(failureCallback) ? failureCallback : $.noop,
			always = $.isFunction(callback) ? callback : $.noop,
			onSuccess = $.proxy(function (result) {
				this.handleResult(result, succeed, fail, always, request, data);
			}, this),
			onError = $.proxy(function (status) {
				var error = new asm.ui.ConnectionError(status || 'Unknown error', request, data);
				this.trigger('error', error);
				var errors = [error];
				fail(errors);
				always(errors);
			}, this);
		this._sendRequest($.extend({}, data || {}, { action: request }), onSuccess, onError);
	},
	/**
	 * Handles core request result.
	 * Provides consistent handling of core request response errors.
	 * @tparam string result response received from server
	 * @tparam function succeed to be called in case of no errors
	 * @tparam function fail @optional to be called in case of some errors
	 * @tparam function always @optional to be called regardless of found errors
	 * @tparam string request @optional name of sent request
	 * @tparam object data @optional arguments of sent request
	 */
	handleResult: function (result, succeed, fail, always, request, data) {
		var response = this._parseResponse(result);
		succeed = $.isFunction(succeed) ? succeed : $.noop;
		fail = $.isFunction(fail) ? fail : $.noop;
		always = $.isFunction(always) ? always : $.noop;

		var failed = false;
		var errors = [];
		if (response.errors && response.errors.length) {
			$.each(response.errors, $.proxy(function (i, error) {
				error = new asm.ui.CoreError(error, request, data);
				this.trigger('error', error);
				errors.push(error);
				if (error.getSeverity() >= asm.ui.Error.ERROR) {
					failed = true;
				}
			}, this));
		}

		if (failed) {
			fail(errors);
		} else if (response.data == undefined) {
			var error = new asm.ui.ConnectionError('No data received from server', request, data);
			this.trigger('error', error);
			fail([error]);
		} else {
			succeed(response.data);
		}
		always(errors);
	}
});
asm.ui.CoreCommunicator.implement(asm.ui.Eventful);