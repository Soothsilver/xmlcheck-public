/**
 * Creates icon from element.
 */
$.widget('ui.icon', {
	options: {
		/** @type mixed
		 * either one of icon names from @ref types to apply @ref typePrefix
		 * + @ref type class to element, or null to disable widget
		 */
		type: null,
		/** @type string
		 * element @c title attribute (hint shown on mouseover)
		 */
		title: '',
		/** @type string
		 * base class applied to element whenever @ref type != null
		 */
		iconClass: 'ui-icon',
		/** @type string
		 * icon type class prefix
		 * @see type
		 */
		typePrefix: 'ui-icon-',
		/** @type array
		 * set of allowed values for @ref type (defaults to set of jQuery UI icon
		 * names)
		 */
		types: ['carat-1-n', 'carat-1-ne', 'carat-1-e', 'carat-1-se', 'carat-1-s',
			'carat-1-sw', 'carat-1-w', 'carat-1-nw', 'carat-2-n-s', 'carat-2-e-w',
			'triangle-1-n', 'triangle-1-ne', 'triangle-1-e', 'triangle-1-se', 'triangle-1-s',
			'triangle-1-sw', 'triangle-1-w', 'triangle-1-nw', 'triangle-2-n-s', 'triangle-2-e-w',
			'arrow-1-n', 'arrow-1-ne', 'arrow-1-e', 'arrow-1-se', 'arrow-1-s',
			'arrow-1-sw', 'arrow-1-w', 'arrow-1-nw', 'arrow-2-n-s', 'arrow-2-ne-sw',
			'arrow-2-e-w', 'arrow-2-se-nw',
			'arrowstop-1-n', 'arrowstop-1-e', 'arrowstop-1-s', 'arrowstop-1-w',
			'arrowthick-1-n', 'arrowthick-1-ne', 'arrowthick-1-e', 'arrowthick-1-se', 'arrowthick-1-s',
			'arrowthick-1-sw', 'arrowthick-1-w', 'arrowthick-1-nw', 'arrowthick-2-n-s', 'arrowthick-2-ne-sw',
			'arrowthick-2-e-w', 'arrowthick-2-se-nw',
			'arrowthickstop-1-n', 'arrowthickstop-1-e', 'arrowthickstop-1-s', 'arrowthickstop-1-w',
			'arrowreturnthick-1-n', 'arrowreturnthick-1-e', 'arrowreturnthick-1-s', 'arrowreturnthick-1-w',
			'arrowreturn-1-n', 'arrowreturn-1-e', 'arrowreturn-1-s', 'arrowreturn-1-w',
			'arrowrefresh-1-n', 'arrowrefresh-1-e', 'arrowrefresh-1-s', 'arrowrefresh-1-w',
			'arrow-4', 'arrow-4-diag',
			'extlink', 'newwin', 'refresh', 'shuffle', 'transfer-e-w', 'transferthick-e-w',
			'folder-collapsed', 'folder-open', 'document', 'document-b', 'note', 'mail-closed',
			'mail-open', 'suitcase', 'comment', 'person', 'print', 'trash', 'locked',
			'unlocked', 'bookmark', 'tag', 'home', 'flag', 'calculator', 'cart', 'pencil',
			'clock', 'disk', 'calendar', 'zoomin', 'zoomout', 'search', 'wrench', 'gear',
			'heart', 'star', 'link', 'cancel', 'plus', 'plusthick', 'minus', 'minusthick',
			'close', 'closethick', 'key', 'lightbulb', 'scissors', 'clipboard', 'copy',
			'contact', 'image', 'video', 'script', 'alert', 'info', 'notice', 'help',
			'check', 'bullet', 'radio-off', 'radio-on', 'pin-w', 'pin-s', 'play', 'pause',
			'seek-next', 'seek-prev', 'seek-end', 'seek-first', 'stop', 'eject', 'volume-off',
			'volume-on', 'power', 'signal-diag', 'signal', 'battery-0', 'battery-1',
			'battery-2', 'battery-3', 'circle-plus', 'circle-minus', 'circle-close',
			'circle-triangle-e', 'circle-triangle-s', 'circle-triangle-w', 'circle-triangle-n',
			'circle-arrow-e', 'circle-arrow-s', 'circle-arrow-w', 'circle-arrow-n',
			'circle-zoomin', 'circle-zoomout', 'circle-check',
			'circlesmall-plus', 'circlesmall-minus', 'circlesmall-close',
			'squaresmall-plus', 'squaresmall-minus', 'squaresmall-close',
			'grip-dotted-vertical', 'grip-dotted-horizontal',
			'grip-solid-vertical', 'grip-solid-horizontal',
			'gripsmall-diagonal-se', 'grip-diagonal-se'
		]
	},
	_create: function () {
		this.element.addClass('ui-icon-widget')
			.addClass(this.options.iconClass);
		this.option('type', this.options.type);
		this._setTitle();
	},
	_setOption: function (key, value) {
		switch (key) {
			case 'type':
				if (value) {
					value = value.replace(new RegExp('^' + this.options.typePrefix), '');
				}
				if ($.inArray(value, this.options.types) == -1) {
					value = (value === null) ? null : false;
				}
				break;
		}
		$.Widget.prototype._setOption.call(this, key, value);
		switch (key) {
			case 'type':
				this._setType();
				break;
			case 'title':
				this._setTitle();
				break;
		}
	},
	_setType: function () {
		var self = this,
			o = this.options;
		$.each(o.types, function (i, type) {
			self.element.removeClass(o.typePrefix + type);
		});
		this.element.removeClass(o.typePrefix + 'transparent');
		if (o.type === false) {
			this.element.removeClass(this.iconClass);
		} else {
			this.element.addClass(o.iconClass)
				.addClass(o.typePrefix + ((o.type === null) ? 'transparent' : o.type));
		}
	},
	_setTitle: function () {
		if (this.options.type === false) {
			this.element.text(this.options.title);
		} else {
			this.element.empty()
				.attr('title', this.options.title);
		}
	}
});