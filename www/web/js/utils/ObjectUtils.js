/**
 * Utility functions focused on working with object properties directly @module.
 */
asm.ui.ObjectUtils = Base.extend({
	constructor: null
}, {
	/**
	 * Gets keys of object properties.
	 * @tparam object obj
	 * @treturn array names of all properties of @a obj
	 */
	keys: function (obj) {
		var ret = [];
		for (var propName in obj) {
			ret.push(propName);
		}
		return ret;
	},
	/**
	 * Gets values of object properties.
	 * @tparam object obj
	 * @treturn array values of all properties of @a obj
	 */
	values: function (obj) {
		var ret = [];
		for (var propName in obj) {
			ret.push(obj[propName]);
		}
		return ret;
	},
	/**
	 * Gets object class (name of constructor function) if possible.
	 * @tparam mixed obj
	 * @treturn mixed name of @a obj constructor function (string), or null if
	 *		@a obj hasn't been created using a constructor function
	 */
	getClass: function (obj) {
		if (obj && obj.constructor && obj.constructor.toString) {
			var arr = obj.constructor.toString().match(/function\s*(\w+)/);
			if (arr && arr.length == 2) {
				 return arr[1];
			}
		}
		return undefined;
	},
	/**
	 * Creates a deep clone of supplied object.
	 * @note Clones only "additional" object properties, not inherent ones like
	 * prototype (do not use to clone class instances).
	 * @tparam mixed obj object or array (distinction will be kept)
	 * @treturn mixed @a obj clone (object or array)
	 */
	clone: function thisFn (obj) {
		var newObj = (obj instanceof Array) ? [] : {};
		$.each(obj, function (key, value) {
			if (value && (typeof value == 'object')) {
				newObj[key] = thisFn(value);
			} else {
				newObj[key] = value;
			}
		});
		return newObj;
	},
	/**
	 * Creates readable string representation of supplied object.
	 * @tparam object obj
	 * @tparam bool recursive @optional set to true to make @a obj property values
	 *		readable in the same fasion
	 *	@treturn string readable representation of @a obj
	 */
	toString: function thisFn (obj, recursive) {
		if ($.isArray(obj)) {
			var items = [];
			for (var i in obj) {
				items.push(recursive ? thisFn(obj[i], recursive) : obj[i]);
			}
			return '[' + items.join(',') + ']';
		} else if (typeof obj == 'object') {
			var members = [];
			for (var i in obj) {
				members.push(i + ':' + (recursive ? thisFn(obj[i], recursive) : obj[i]));
			}
			return '{' + members.join(',') + '}';
		} else {
			return obj.toString();
		}
	}
});