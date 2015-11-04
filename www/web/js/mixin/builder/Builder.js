/**
 * Adds convenience methods for building of content.
 */
asm.ui.Builder = Base.extend({
	/** Element to build other elements in (hidden, accessible). */
	_builderCanvas: $('<div></div>')
        .addClass('ui-helper-hidden-accessible'),
	/** Names of tags that can be used as single tags in XHTML. */
	_singleTags: ['area', 'base', 'br', 'col', 'frame', 'hr', 'img', 'input', 'link', 'meta', 'param'],
	/**
	 * Creates XHTML element with supplied tag name and attributes.
	 * @tparam string tag tag name
	 * @tparam object attributes tag attributes
	 * @treturn jQueryEl created element
	 */
	_buildTag: function (tag, attributes) {
        /*
        if (this._builderCanvas === undefined)
        {
            this._builderCanvas = $('<div></div>')
                .addClass('ui-helper-hidden-accessible')
                .appendTo('body');
        }
        */
		var str = '<' + tag;

		if (attributes != undefined) {
			$.each(attributes, function (name, value) {
				str += ' ' + name + '="' + value + '"';
			});
		}

		str += ($.inArray(tag, this._singleTags) != -1)
			? '/>'
			: '></' + tag + '>';

        var newTag = $(str);
		return newTag.appendTo(this._builderCanvas);
	},
	/**
	 * Creates link element (optionally with icon).
	 * @tparam string href
	 * @tparam string text
	 * @tparam string icon @optional icon name
	 * @treturn jQueryEl created link element
	 */
	_buildLink: function (href, text, icon) {
		var link = this._buildTag('a').attr('href', href)
			.addClass('compositeLink');

		if (icon != undefined) {
			this._buildTag('span').icon({ type: icon })
				.appendTo(link);
		}

		this._buildTag('span').addClass('linkText')
			.append(text)
			.appendTo(link);

		return link;
	},
	/**
	 * Creates link element pointing out of the application.
	 * Link will have appropriate visual style and its target will open in a new
	 * window.
	 * @tparam string href
	 * @tparam string text
	 * @treturn jQueryEl created link element
	 */
	_buildOutLink: function (href, text) {
		return this._buildLink(href, text, 'extlink')
			.attr('target', '_blank')
			.addClass('outLink');
	},
	/**
	 * Creates button element with supplied onClick handler & visual properties.
	 * @tparam function action onClick handler
	 * @tparam string label button text (empty to show no text)
	 * @tparam string primaryIcon icon class for primary icon
	 * @tparam string secondaryIcon icon class for secondary icon
	 * @treturn jQueryEl created button element
	 */
	_buildButton: function (action, label, primaryIcon, secondaryIcon) {
		return this._buildTag('button')
			.button({
				text: !!label,
				icons: {
					primary: primaryIcon || null,
					secondary: secondaryIcon || null
				},
				label: label
			})
			.click(action);
	}
});