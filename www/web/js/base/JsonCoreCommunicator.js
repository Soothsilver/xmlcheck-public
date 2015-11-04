/**
 * Core communicator using JSON formatting of requests & responses.
 * @note No other communication formats are supported at this time.
 */
asm.ui.JsonCoreCommunicator = asm.ui.CoreCommunicator.extend({
	/**
	 * Initializes instance with supplied configuration.
	 * @tparam object config configuration options
	 *
	 * Supported configuration options:
	 * @arg @a url server url
	 */
	constructor: function (config) {
		var defaults = {
			url: '.'
		};
		this.config = $.extend(defaults, config);
	},
	/**
	 * @copydoc CoreCommunicator::_sendRequest()
	 *
	 * Sends core request using AJAX POST.
	 */
	_sendRequest: function (data, successCallback, failureCallback) {
		$.ajax({
			type: 'POST',
			url: this.config.url,
			global: false,
			data: data,
			dataType: 'text',
			context: this,
			success: successCallback,
			error: function (xhr, textStatus, errorThrown) {
				failureCallback(textStatus);
			}
		});
	},
	/**
	 * @copydoc CoreCommunicator::_parseResponse()
	 *
	 * Parses JSON string into object with response data.
	 */
	_parseResponse: function (response) {
		// h4ck - utf-8 BOM in PHP json_encode
		response = response.replace(/\ufeff/g, '', response);
        try
        {
		    return $.secureEvalJSON(response);
        }
        catch (error)
        {
            // Something that is not valid JSON got returned
			return {
				data: [],
				errors: [
					{
						"level": 100, // Fatal error
						"code": "= no code =",
						"cause": "Invalid response",
						"effect": "PHP",
						"details": response

					}
				]
			};
        }
	}
});
