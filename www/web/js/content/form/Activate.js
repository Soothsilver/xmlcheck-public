/**
 * Activate newly registered user account.
 */
asm.ui.form.Activate = asm.ui.DynamicForm.extend({
	constructor: function (config) {
		var defaults = {
			formProps: {
				simple: true,
				submitText: asm.lang.activationScreen.activateButton
			},
			formStructure: { main: {
				icon: asm.ui.globals.icons.activate,
				caption: asm.lang.activationScreen.caption,
				fields: {
					code: {
						type: 'text',
						label: asm.lang.activationScreen.activationCode,
					}
				}
			}},
			request: 'Activate'
		};
		this.base($.extend(true, defaults, config));
	}
});