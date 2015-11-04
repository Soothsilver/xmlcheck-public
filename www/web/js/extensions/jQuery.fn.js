$ = $ || jQuery;

$.fn.css2 = $.fn.css;

/**
 * %jQuery extensions.
 * Non-static methods are additional methods callable on %jQuery selections.
 */
$.fn.extend({
	/**
	 * Extension of jQuery.css (returns element styles if no arguments are supplied).
	 * In case any arguments are supplied, original jQuery.fn.css() is called.
	 */
	css: function() {
		if (arguments.length) {
			return $.fn.css2.apply(this, arguments);
		}
		var attr = ['font-family','font-size','font-weight','font-style','color',
				'text-transform','text-decoration','letter-spacing','word-spacing',
				'line-height','text-align','vertical-align','direction','background-color',
				'background-image','background-repeat','background-position',
				'background-attachment','opacity','width','min-width','max-width',
				'height','min-height','max-height','top','right','bottom',
				'left','margin-top','margin-right','margin-bottom','margin-left',
				'padding-top','padding-right','padding-bottom','padding-left',
				'border-top-width','border-right-width','border-bottom-width',
				'border-left-width','border-top-color','border-right-color',
				'border-bottom-color','border-left-color','border-top-style',
				'border-right-style','border-bottom-style','border-left-style','position',
				'display','visibility','z-index','overflow-x','overflow-y','white-space',
				'clip','float','clear','cursor','list-style-image','list-style-position',
				'list-style-type','marker-offset'];
		var len = attr.length, obj = {};
		for (var i = 0; i < len; i++)
		  obj[attr[i]] = $.fn.css2.call(this, attr[i]);
		return obj;
	},
	/**
	 * Gets HTML representation of contained elements.
	 * @note Doesn't work on XML elements.
	 * @treturn string HTML string
	 */
	outerHtml: function () {
		var html = '';
		this.each(function () {
			var el = $(this);
			html += el.wrap('<div>').parent().html();
			el.unwrap();
		});
		return html;
	},
	/**
	 * Sets unique values (with supplied prefix and/or suffix) to 'id' attribute
	 * of contained elements.
	 * @tparam string prefix @optional ID prefix
	 * @tparam string suffix @optional ID suffix
	 * @treturn jQuerySel self
	 */
	uniqueId: function (prefix, suffix) {
		var randomId = (+new Date()),
			prefix = prefix || '',
			suffix = suffix || '';

		this.each(function () {
			var step = 1,
				id = prefix + randomId + suffix;
			do {
				randomId += step;
				step *= 2;
				id = prefix + randomId + suffix;
			} while ($('#' + id).length);
			$(this).attr('id', id);
		});

		return this;
	}
});