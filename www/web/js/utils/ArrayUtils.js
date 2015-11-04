/**
 * Array-oriented utility functions @module.
 */
asm.ui.ArrayUtils = Base.extend({
	constructor: null
}, {
	/**
	 * Replacement of Array.indexOf (missing from JavaScript core in IE).
	 * @tparam mixed needle
	 * @tparam array haystack
	 * @tparam int offset @optional
	 * @treturn int index of @a needle in @a haystack, or -1 if not found
	 */
	indexOf: function (needle, haystack, offset) {
		for (var i = (offset || 0); i < haystack.length; i++) {
			if (haystack[i] == needle) {
				return i;
			}
		}
		return -1;
	},
	/**
	 * Removes element from array.
	 * @tparam mixed needle
	 * @tparam array haystack
	 * @tparam int offset @optional
	 * @note Modifies the original array!
	 */
	remove: function (needle, haystack, offset) {
		var index = asm.ui.ArrayUtils.indexOf(needle, haystack, offset);
		if (index >= 0) {
			haystack.splice(index, 1);
		}
	},
	/**
	 * Checks array for existence of supplied value.
	 * Returns a bolean, unlike indexOf function (JavaScript core), which returns
	 * and index (as well as jQuery.inArray() function).
	 * @tparam mixed needle
	 * @tparam array haystack
	 * @tparam bool argStrict @optional set to true to use strict equivalence checking
	 * @treturn bool true if @a haystack contains @a needle value
	 */
	inArray: function (needle, haystack, argStrict) {
		var key = '',
			strict = !!argStrict;

		if (strict) {
			for (key in haystack) {
				if (haystack[key] === needle) {
					return true;
				}
			}
		} else {
			for (key in haystack) {
				if (haystack[key] == needle) {
					return true;
				}
			}
		}

		return false;
	},
	/**
	 * Checks whether two arrays are the same.
	 * @tparam array a first array
	 * @tparam array b second array
	 * @tparam bool strict @optional set to true to use strict equivalence checking
	 * @treturn bool true if @a a and @a b contain the same values at same indexes
	 */
	compare: function (a, b, strict) {
		if (a.length != b.length) {
			return false;
		}
		for (var i = 0; i < a.length; ++i) {
			if ((strict && (a[i] !== b[i])) || (a[i] != b[i])) {
				return false;
			}
		}
		return true;
	},
	/**
	 * Combines two arrays into one using first as keys and second as values.
	 * @tparam array keys
	 * @tparam array values
	 * @treturn mixed new array, or null if @a keys and @a values have different
	 *		number of values
	 */
	combine: function (keys, values) {
		if ((keys.length == undefined) || (values.length == undefined) || (keys.length != values.length)) {
			return null;
		}

		var ret = {};
		for (var i = 0; i < keys.length; ++i){
			ret[keys[i]] = values[i];
		}
		return ret;
	}
});