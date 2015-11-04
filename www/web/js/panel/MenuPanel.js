/**
 * Panel containing a menu.
 */
asm.ui.MenuPanel = asm.ui.ContentPanel.extend({
	/**
	 * @copydoc ContentPanel::ContentPanel()
	 *
	 * Additional configuration options:
	 * @arg @a currentItem initially selected menu item (defaults to '')
	 * @arg @a separator string used to join display parameters into single menu item value
	 * @arg @a structure menu structure accepted by MenuBuilder::_buildMenu()
	 */
	constructor: function (config) {
		var defaults = {
			currentItem: '',
			separator: '#',
			structure: {}
		};
		this.base($.extend(defaults, config));

		this._inputName = asm.ui.Utils.getUniqueElementId();
		this._menuEl = null;
		this._defEl = null;
	},
	/**
	 * @copybrief ContentPanel::_buildContent()
	 * 
	 * Builds menu. Menu items trigger <tt>menu.switch</tt> event.
	 */
	_buildContent: function () {
		var o = this.config;

		this._menuEl = this._buildMenu(this._inputName, o.structure, o.separator)
			.appendTo(o.target);

		$(':radio[name=' + this._inputName + ']', this._menuEl)
			.bind('change.menuPanel', $.proxy(function (event) {
				if (event.currentTarget.checked && !this._adjusting) {
					this._triggerMenuSwitch(event.currentTarget.value.split(this.config.separator));
				}
			}, this));

		this._defEl = $('<input type="radio"/>')
			.attr('name', this._inputName)
			.css('display', 'none')
			.appendTo(o.target);
	},
	/**
	 * @copybrief ContentPanel::_adjustContent()
	 *
	 * Selects menu item based on display parameters. Joins parameters by set
	 * separator (see @ref MenuPanel::MenuPanel() "constructor"). Applies selection
	 * style classes to selected items and all parent submenu headers.
	 */
	_adjustContent: function () {
		var o = this.config;
		
		$('.submenu-active', this._menuEl).removeClass('submenu-active');

		var pathArray = this._params,
			value = pathArray.join(o.separator),
			matchingRadio = $(':radio[name=' + this._inputName + ']', this._menuEl).not(this._defEl.get(0))
				.filter(function () {
					return ((this.value == value)
							|| (asm.ui.ArrayUtils.indexOf(this.value, pathArray) == 0));
				}),
			noMatch = (matchingRadio.length == 0);

		if (noMatch) {
			this._defEl.attr('checked', true);
		} else {
			matchingRadio.attr('checked', true)
				.parents('.submenu').prev('.submenu-header').addClass('submenu-active');
		}
	},
	/**
	 * Triggers <tt>menu.switch</tt> event with following data:
	 * @li @c value array with address parts
	 */
	_triggerMenuSwitch: function (value) {
		this.trigger('menu.switch', {value: value})
	}
});
asm.ui.MenuPanel.implement(asm.ui.MenuBuilder);