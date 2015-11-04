/**
 * Register new user form.
 */
asm.ui.form.ResetPassword = asm.ui.DynamicForm.extend({
	constructor: function (config) {
		var defaults = {
			formProps: {
				submitText: asm.lang.resetPasswordScreen.submit
			},
            classes: [ 'content-register' ],
			formStructure: { main: {
				icon: asm.ui.globals.icons.user,
				caption: asm.lang.resetPasswordScreen.caption,
				fields: {
					resetLink: {
						type: 'text',
						value: '',
                        label: asm.lang.resetPasswordScreen.resetLink
                    },
					pass: {
						label: asm.lang.resetPasswordScreen.password,
						type: 'password',
						hint: asm.lang.resetPasswordScreen.passwordHint,
						check: ['hasLength'],
						checkParams: { minLength: asm.ui.constants.passwordMinLength, maxLength: asm.ui.constants.passwordMaxLength }
					},
					repass: {
						label: asm.lang.resetPasswordScreen.retypePassword,
						type: 'password',
						hint: asm.lang.resetPasswordScreen.retypePasswordHint,
						check: function (value, field) {
							return (value != field.prev().field('option', 'value'))
								? asm.lang.resetPasswordScreen.retypePasswordError : false;
						}
					}
				}
			}},
			request: 'ResetPassword'
		};
		this.base($.extend(true, defaults, config));
	},
    _adjustContent: function() {
        this.fill({
            resetLink: this._params[0]
        });
    }
});