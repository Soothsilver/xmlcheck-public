/**
 * Manages downloading of files from server.
 */
asm.ui.FileDownloader = Base.extend({
	/**
	 * Initializes instance with supplied configuration.
	 * @tparam object config configuration options
	 *
	 * Supported configuration options:
	 * @arg @a url server url
	 * @arg @a resultHandler callback to be called on core request result
	 */
	constructor: function (config) {
		var defaults = {
			url: null,
			resultHandler: null
		};
		this.config = $.extend(defaults, config);
		this.data = {};
	},
	_createWindow: function (url) {
		throw 'Not implemented!';
	},
	/**
	 * Removes all garbage created while handling previous requests.
	 */
	cleanup: function () {
	},
	trash: function (window) {
	},
	/**
	 * Sends core request for file download.
	 * @tparam string request request name
	 * @tparam object data request arguments
	 * @tparam function successCallback to be called on request success
	 * @tparam function failureCallback to be called on request failure
	 */
	request: function (request, data, successCallback, failureCallback) {
		this.cleanup();

		var params = [];
		$.each($.extend({}, data, { action: request }), function (key, value) {
			params.push(escape(key) + '=' + escape(value));
		});
		var url = this.config.url + '?' + params.join('&'),
			eventData = {
				request: request,
				arguments: data,
				handle: this.config.resultHandler,
				succeed: $.isFunction(successCallback) ? successCallback : $.noop,
				fail: $.isFunction(failureCallback) ? failureCallback : $.noop,
				trash: $.proxy(this.trash, this)
			},
			myWindow = this._createWindow(url);
		$(myWindow).bind('load', eventData, function (event) {
			var myWindow = event.currentTarget,
				contentWindow = myWindow.contentWindow,
				loaded = (contentWindow.location != 'about:blank'),
				response = contentWindow.document.body.innerHTML,
				o = event.data;
			if (loaded) {
				o.handle([response, o.succeed, o.fail, null, o.request, o.arguments], myWindow);
			}
			o.trash(myWindow);
		});
	}
});