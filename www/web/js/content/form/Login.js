/**
 * Login form.
 */
asm.ui.form.Login = asm.ui.DynamicForm.extend({
	constructor: function (config) {
		var defaults = {
			formProps: {
				simple: true,
				submitText: asm.lang.loginScreen.loginButton
            },
			formStructure: { main: {
				icon: asm.ui.globals.icons.login,
				caption: asm.lang.loginScreen.caption,
				fields: {
					name: {
						type: 'text',
						label: asm.lang.loginScreen.username
					},
					pass: {
						type: 'password',
						label: asm.lang.loginScreen.password
					}
				}
			}},
			request: 'Login'
		};
		this.base($.extend(true, defaults, config));
	}
});