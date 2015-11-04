/**
 * User interface settings form (offline, interacts with UI directly).
 */
asm.ui.form.UiSettings = asm.ui.DynamicForm.extend({
	constructor: function (config) {
		var defaults = {
			formProps: {
				offline: true
			},
			formStructure: { main: {
				icon: asm.ui.globals.icons.settings,
				caption: asm.lang.userInterface.caption,
				fields: {
					theme: {
						type: 'select',
						label: asm.lang.userInterface.visualTheme,
						value: asm.ui.globals.config.get('theme'),
						options: asm.ui.ArrayUtils.combine(asm.ui.globals.themes,
							$.map(asm.ui.globals.themes, function (value) {
								return asm.ui.StringUtils.ucwords(value.replace(/-/, ' '));
							}
						)),
						check: function (value) {
							asm.ui.globals.config.set(value, 'theme');
							return false;
						}
					}
				}
			}}
		};
		this.base($.extend(true, defaults, config));
	}
});