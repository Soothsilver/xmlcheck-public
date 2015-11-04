/**
 * Extension of core %String class.
 */
$.extend(String.prototype, {
	/**
	 * Checks whether this string ends with supplied suffix.
	 * @tparam string suffix
	 * @treturn bool true if @c this ends with @a suffix
	 */
	endsWith: function (suffix) {
		 return this.substr(this.length - suffix.length) === suffix;
	},
	/**
	 * Checks whether this string starts with supplied prefix.
	 * @tparam string prefix
	 * @treturn bool true if @c this ends with @a prefix
	 */
	startsWith: function (prefix) {
		 return this.substr(0, prefix.length) === prefix;
	},
	/**
	 * Removes trailing whitespace from this string.
	 * @treturn string @c this with trailing whitespace removed
	 */
	trimEnd: function () {
		 return this.replace(/\s+$/, "");
	},
	/**
	 * Removes leading whitespace from this string.
	 * @treturn string @c this with leading whitespace removed
	 */
	trimStart: function () {
		 return this.replace(/^\s+/, "");
	},
	/**
	 * Removes leading and trailing whitespace from this string.
	 * @treturn string @c this with leading and trailing whitespace removed
	 */
	trim: function() {
		 return this.replace(/^\s*/, '').replace(/\s*$/, '');
	}
});