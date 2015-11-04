/**
 * @copybrief Builder
 * 
 * Adds methods for building of menus.
 */
asm.ui.MenuBuilder = asm.ui.Builder.extend({
	/**
	 * Builds menu from supplied data.
	 * @tparam string name name attribute of all radio elements used in this menu
	 * @tparam object content menu content data in following format:
	 *	@code
	 *	{
	 *		'Submenu 1 label': {
	 *			'Subsubmenu 1.1 label': {
	 *				... // nested
	 *			},
	 *			'Submenu item 1.2 label': ['address', 'parts'] // e.g. ['login'] or ['settings', 'ui']
	 *		},
	 *		...
	 *	}
	 *	@endcode
	 * @tparam string separator menu item value parts will be joined with this string
	 * @treturn jQueryEl created menu element
	 */
	_buildMenu: function thisFn (name, content, separator) {
		var menu = this._buildTag('div').addClass('menu');

		for (var menuName in content) {
			var items = content[menuName],
				header = this._buildTag('h3').addClass('submenu-header').append(asm.lang.menu[menuName]),
				body = this._buildTag('div').addClass('submenu');
				
			for (var item in items) {
				var itemLabel = items[item];
				
				if (typeof item == 'string') {
					item = [item];
				}

				if (!$.isArray(item)) {
					thisFn(name, item, separator).appendTo(body);
				} else {
					var value = item.join(separator);
					$('<input type="radio"/>')
						.attr('id', name + value)
						.attr('name', name)
						.attr('value', value)
						.appendTo(body);
					this._buildTag('label')
						.attr('for', name + value)
						.append(itemLabel)
						.appendTo(body);
				}
			}

			if (body.children().length) {
				menu.append(header)
					.append(body);
			}
		}

		return menu;
	},
	/**
	 * Enhances supplied menu visually (accordion-style).
	 * @tparam jQueryEl menu menu created by _buildMenu()
	 * @tparam object icons menu item values (address parts joined by separator)
	 *		as keys and icon names as values
	 *	@treturn jQueryEl @a menu enhanced with icons and turned into accordion
	 *		widget
	 */
	_buildAccordionMenu: function (menu, icons) {
		menu.find(':radio')
			.each(function () {
				var icon = (icons[this.value] != undefined)
						? 'ui-icon-' + icons[this.value] : 'ui-icon-triangle-1-e';
				$(this).button({icons: {primary: icon}});
				$('label[for=' + $(this).attr('id') + ']', menu)
					.addClass('ui-border-none')
					.addClass('ui-background-solid')
					.corner({styles: null})
					.filter(':last-child')
						.corner({styles: ['bottom']});
			});

		menu.find('h3').wrapInner('<a href="#"></a>');

		menu.accordion({
				animated: false,
				collapsible: true,
				clearStyle: true
			});

		for (var i = 0; i < menu.children('h3').length; ++i) {
			menu.accordion('activate', i);
		}
		
		menu.accordion('activate', 0)
			.accordion('option', 'collapsible', false)
			.accordion('option', 'animated', 'slide');

		return menu;
	}
});