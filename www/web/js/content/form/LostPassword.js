/**
 * Activate newly registered user account.
 */
asm.ui.form.LostPassword = asm.ui.DynamicForm.extend({
	constructor: function (config) {
		var defaults = {
			formProps: {
				simple: true,
				submitText: asm.lang.lostPasswordScreen.resetButton
			},
            classes: ['content-login'],
			formStructure: { main: {
				icon: asm.ui.globals.icons.lostPassword,
				caption: asm.lang.lostPasswordScreen.caption,
				fields: {
					email: {
						type: 'text',
						label: asm.lang.lostPasswordScreen.email,
                        check: ['isNotEmpty', 'isEmail']
					}
				}
			}},
			request: 'RequestResetLink'
		};
		this.base($.extend(true, defaults, config));
	}
});