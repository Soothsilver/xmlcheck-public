/**
 * Manages showing/hiding of element's text content overflowing specified length.
 */
$.widget('ui.textCutter', {
	options: {
		/**
		 * how many characters will be always shown (false to disable widget)
		 * @note When limit is set, text may sometimes be shortened more than just
		 *		to set limit (see _findCutIndex()).
		 */
		limit: false,
		/** true == hide overflow */
		hidden: true,
		/** show ellipsis in place of overflow when overflow is hidden */
		showEllipsis: false,
		/** show overflow visibility toggler if text is longer than @ref limit */
		showToggler: true
	},
	_create: function () {
		this.element.addClass('ui-textCutter');
		
		this._isCut = false;
		
		this.option('limit', this.options.limit);
		this.option('hidden', this.options.hidden);
		this.option('showEllipsis', this.options.showEllipsis);
		this.option('showToggler', this.options.showToggler);
	},
	_setOption: function (key, value) {
		$.Widget.prototype._setOption.apply(this, arguments);
		switch (key) {
			case 'limit':
				this._setLimit();
				// fallthrough to 'hidden'
			case 'hidden':
				this._setHidden();
				this._setShowEllipsis();
				this._setShowToggler();
				break;
			case 'showEllipsis':
				this._setShowEllipsis();
				break;
			case 'showToggler':
				this._setShowToggler();
				break;
		}
	},
	_setHidden: function () {
		this.element[(this.options.hidden ? 'add' : 'remove') + 'Class']('ui-textCutter-hidden');
	},
	_setLimit: function () {
		this._destroyCutter();
		this._createCutter();
	},
	_setShowEllipsis: function () {
		this.element[(this.options.showEllipsis ? 'remove' : 'add') + 'Class']('ui-textCutter-no-ellipsis');
	},
	_setShowToggler: function () {
		this.element[(this.options.showToggler ? 'remove' : 'add') + 'Class']('ui-textCutter-no-toggler');
	},
	/**
	 * Unwraps overflow content from wrapper elements.
	 */
	_destroyCutter: function () {
		$('.ui-textCutter-overflow', this.element).removeClass('ui-textCutter-overflow');
		$('.ui-textCutter-overflow-text', this.element).each(function () {
			$(this).after($(this).text()).remove();
		});
	},
	/**
	 * Wraps overflow content in wrapper elements for easy hiding.
	 */
	_createCutter: function () {
		var limit = this.options.limit;
		if (!limit || (this.element.text().length <= limit)) {
			this._isCut = false;
			return;
		}
		this._isCut = true;

		var nodeSet = this.element.contents(),
			node = nodeSet.first();
		while ((limit > 0) && (node.length) && (node.get()[0].nodeType != Node.TEXT_NODE)) {
			var nodeTextLength = node.text().length;
			if (nodeTextLength > limit) {
				node.nextAll().addClass('ui-textCutter-overflow');
				nodeSet = node.contents();
				node = nodeSet.first();
			} else {
				limit -= nodeTextLength;
				node = node.next();
			}
		}
		if ((limit > 0) && (node.length)) {
			var text = node.text(),
				cutIndex = this._findCutIndex(text, limit),
				visibleText = text.substr(0, cutIndex),
				overflow = text.substr(cutIndex),
				toggler = $('<span></span>').addClass('ui-textCutter-toggler')
					.addClass('ui-border-none')
					.mouseover(function (event) {
						$(event.currentTarget).addClass('ui-state-highlight')
							.addClass('ui-clickable');
					})
					.mouseout(function (event) {
						$(event.currentTarget).removeClass('ui-state-highlight')
							.removeClass('ui-clickable');
					})
					.click($.proxy(function (event) {
						event.stopPropagation();
						this.toggle();
						$(event.currentTarget).mouseout();
					}, this)),
				lastNode;
			nodeSet.slice(nodeSet.index(node) + 1).each(function () {
				if (this.nodeType != Node.TEXT_NODE) {
					lastNode = $(this).addClass('ui-textCutter-overflow');
				} else {
					lastNode = $('<span></span>').text(this.textContent)
							.addClass('ui-textCutter-overflow-text');
					$(this).after(lastNode)
						.remove();
				}
			});
			var overflowTextNode = $('<span></span>')
				.addClass('ui-textCutter-overflow-text')
				.text(overflow);
			node.after(overflowTextNode)
				.after(visibleText)
				.remove();
			(lastNode || overflowTextNode).after(toggler)
				.after($('<span></span>')
					.addClass('ui-textCutter-ellipsis')
					.addClass('ui-border-none'))
				.after($('<span></span>')
					.addClass('ui-virtual-space'));
		}
	},
	/**
	 * Searches supplied string for position where overflow starts.
	 * Overflow start is either after first newline, at last space before
	 * @a "limit"'th character, or at @a "limit"'th character, whichever
	 * comes first.
	 * @tparam string text
	 * @tparam int limit cut index maximum
	 * @treturn int index of character in @a text where it's best to cut
	 */
	_findCutIndex: function (text, limit) {
		var roughCut = text.substr(0, limit),
			firstNewlinePos = roughCut.search(/\n/);
		if (firstNewlinePos != -1) {
			return firstNewlinePos;
		}
		if (roughCut.search(/\s/) != -1) {
			var regexp = /^.*\s/,
				niceCut = regexp.exec(roughCut)[0];
			return niceCut.length;
		}
		return limit;
	},
	destroy: function () {
		this.element.removeClass('ui-textCutter');
		this._destroyCutter();
		$.Widget.prototype.destroy.call(this);
	},
	/**
	 * Checks whether overflow wrappers are currently created.
	 * @treturn bool
	 */
	isCut: function () {
		return this._isCut;
	},
	/**
	 * Toggles visibility of overflow.
	 * @tparam bool show @optional set to true to show overflow, leave undefined
	 *		to invert current visibility
	 */
	toggle: function (show) {
		if (show === undefined) {
			show = this.options.hidden;
		}
		this.option('hidden', !show);
	},
	/**
	 * Shows overflow.
	 */
	show: function () {
		this.toggle(true);
	},
	/**
	 * Hides overflow.
	 */
	hide: function () {
		this.toggle(false);
	}
});