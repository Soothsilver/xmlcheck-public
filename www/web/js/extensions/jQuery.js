$ = $ || jQuery;
// Adds the function 'cookie' to jQuery object

/**
 * %jQuery extensions.
 * Static methods are basically public functions in @c jQuery (@c $) namespace
 * declared this way to indicate they are available for use by widgets.
 */
$.extend({
	/**
	 * Gets or sets cookie (depending on supplied arguments).
	 * @tparam string key name of cookie to get/set
	 * @tparam mixed value @optional cookie value to be set
	 * @tparam object options @optional additional cookie parameters:
	 * @arg @a expires (Date) cookie expiration date
	 * @arg @a domain
	 * @arg @a path
	 * @arg @a secure
	 * @treturn mixed returns cookie value if @a value wasn't supplied, nothing
	 *		otherwise
	 */
	cookie: function (key, value, options) {
		if (value !== undefined) { // setter
			var defaults = {
				expires: 1,
				path: null,
				domain: null,
				secure: false
			};
			options = $.extend(defaults, options);
			if (value === null) {
				value = '';
				options.expires = -1;
			}
			if (!options.expires.toUTCString) {
				var date = new Date();
				date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
				options.expires = date;
			}
			document.cookie = [
					key, '=', encodeURIComponent(value),
					'; expires=' + options.expires.toUTCString(),
					(options.path ? '; path=' + (options.path) : ''),
					(options.domain ? '; domain=' + (options.domain) : ''),
					(options.secure ? '; secure' : '')
				].join('');
		} else { // getter
			var getAll = (key == undefined),
				ret = getAll ? {} : null;
			if (document.cookie) {
				var cookies = document.cookie.split(';');
				$.each(cookies, function (i, cookie) {
					cookie = $.trim(cookie);
					if (getAll) {
						var parts = cookie.split('=');
						ret[parts.shift()] = decodeURIComponent(parts.join('='));
					} else if (cookie.startsWith(key + '=')) {
						ret = decodeURIComponent(cookie.substring(key.length + 1));
						return false; // break $.each()
					}
				});
			}
			return ret;
		}
	}
});