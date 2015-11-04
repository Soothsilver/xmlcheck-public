/**
 * Enhances MenuPanel with accordion menu style.
 */
asm.ui.AccordionMenuPanel = asm.ui.MenuPanel.extend({
	/**
	 * @copydoc MenuPanel::MenuPanel()
	 *
	 * Additional configuration options:
	 * @arg @a icons object with custom icons for menu items (see
	 *		MenuBuilder::_buildAccordionMenu())
	 */
	constructor: function (config) {
		var defaults = {
			icons: {}
		};
		this.base($.extend(defaults, config));
	},
	/**
	 * @copydoc MenuPanel::_buildContent()
	 *
	 * Adds accordion menu look & feel to menu.
	 */
	_buildContent: function () {
		this.base();
		this._buildAccordionMenu(this._menuEl, this.config.icons);
	},
	_adjustContent: function () {
		this.base();

		$(':radio[name=' + this._inputName + ']', this._menuEl).not(this._defEl.get(0))
			.button('refresh');
		this._menuEl.accordion('activate', '.submenu-active');
	}
});