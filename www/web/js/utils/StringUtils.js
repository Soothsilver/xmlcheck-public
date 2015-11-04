/**
 * String-oriented utility functions @module.
 */
asm.ui.StringUtils = Base.extend({
	constructor: null
}, {
	/**
	 * Turns first letter of supplied string to upper-case.
	 * @tparam string str
	 * @treturn string @a str with first letter upper-cased
	 */
	ucfirst: function (str) {
		str += '';
		var f = str.charAt(0).toUpperCase();
		return f + str.substr(1);
	},
	/**
	 * Uppercases first letters of every word in supplied string.
	 * @tparam string str
	 * @treturn string @a str with first letters of all words upper-cased
	 */
	ucwords: function (str) {
		return (str + '').replace(/^(.)|\s(.)/g, function (chr) {
        return chr.toUpperCase();
	  });
	},
	/**
	 * Replaces characters with special significance in HTML with their corresponding
	 * HTML entities in supplied string.
	 * @tparam string str
	 * @treturn string @a str with '&amp;', '&lt;', and '&gt;' replaced
	 */
	htmlspecialchars: function thisFn (str) {
		if (thisFn.subs === undefined) {
			thisFn.subs = [[/&/g, '&amp;'], [/</g, '&lt;'], [/>/g, '&gt;']];
		}
		for (var i in thisFn.subs) {
			str = str.replace(thisFn.subs[i][0], thisFn.subs[i][1]);
		}
		return str;
	}
});