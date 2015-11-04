/**
 * Register new user form.
 */
asm.ui.form.Register = asm.ui.DynamicForm.extend({
	constructor: function (config) {
		var defaults = {
			formProps: {
				submitText: asm.lang.registrationScreen.registerButton
			},
			formStructure: { main: {
				icon: asm.ui.globals.icons.user,
				caption: asm.lang.registrationScreen.caption,
				fields: {
					type: {
						type: 'hidden',
						value: '1' // This is a code for "create new user with default role". The basic usertype Student has always ID 1.
					},
                    fromRegistrationForm: {
                        type: 'hidden',
                        value: '1'
                    },
					realname: {
						label: asm.lang.registrationScreen.fullName,
						type: 'text',
						hint: asm.lang.registrationScreen.fullnameHelp,
						check: ['isNotEmpty', 'isName']
					},
					email: {
						label: asm.lang.registrationScreen.email,
						type: 'text',
						hint: asm.lang.registrationScreen.emailHelp,
						check: ['isNotEmpty', 'isEmail']
					},
					name: {
						label: asm.lang.registrationScreen.username,
						type: 'text',
						hint: asm.lang.registrationScreen.usernameHelp,
						check: ['isAlphaNumeric', 'hasLength', asm.ui.Macros.nameCheck('users')],
						checkParams: { minLength: asm.ui.constants.usernameMinLength, maxLength: asm.ui.constants.usernameMaxLength }
					},
					pass: {
						label: asm.lang.registrationScreen.password,
						type: 'password',
						hint: asm.lang.registrationScreen.passwordHelp,
						check: ['hasLength'],
						checkParams: { minLength: asm.ui.constants.passwordMinLength, maxLength: asm.ui.constants.passwordMaxLength }
					},
					repass: {
						label: asm.lang.registrationScreen.retypePassword,
						type: 'password',
						hint: asm.lang.registrationScreen.retypePasswordHelp,
						check: function (value, field) {
							return (value != field.prev().field('option', 'value'))
								? asm.lang.registrationScreen.retypePasswordError : false;
						}
					}
				}
			}},
			request: 'EditUser'
		};
		this.base($.extend(true, defaults, config));
	}
});