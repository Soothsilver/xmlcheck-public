/**
 * Form for changing own user account properties.
 */
asm.ui.form.UserAccount = asm.ui.DynamicForm.extend({
	constructor: function (config) {
		var defaults = {
			callbacks: {
				success: function (response, data) {
					asm.ui.globals.session.setRealName(data.realname);
					asm.ui.globals.session.setEmail(data.email);
					asm.ui.globals.stores.users.expire();
				}
			},
			formStructure: { main: {
				icon: asm.ui.globals.icons.account,
				caption: asm.lang.accountSettings.caption,
				fields: {
					id: {
						type: 'hidden'
					},
					name: {
						type: 'hidden'
					},
					realname: {
						label: asm.lang.accountSettings.fullname,
						type: 'text',
						hint: asm.lang.accountSettings.nameHint,
						check: ['isNotEmpty', 'isName']
					},
					email: {
						label: asm.lang.accountSettings.email,
						type: 'text',
						check: ['isNotEmpty', 'isEmail']
					},
					pass: {
						label: asm.lang.accountSettings.newPassword,
						type: 'password',
						hint: asm.lang.accountSettings.passwordHint,
						check: function(value, field) {
							if (value.length == 0) { return false; }
							if (value.length < asm.ui.constants.passwordMinLength) { return asm.lang.accountSettings.tooFewCharactersError;}
							if (value.length > asm.ui.constants.passwordMaxLength) { return asm.lang.accountSettings.tooManyCharactersError }
							return false;
						}
					},
					repass: {
						label: asm.lang.accountSettings.retypeNewPassword,
						type: 'password',
						hint: asm.lang.accountSettings.retypeHint,
						check: function (value, field) {
							return (value != field.prev().field('option', 'value'))
								? asm.lang.accountSettings.retypeError: false;
						}
					}
				}
			}},
			request: 'EditUser'
		};
		this.base($.extend(true, defaults, config));
	},
	_adjustContent: function () {
		var get = $.proxy(asm.ui.globals.session.getProperty, asm.ui.globals.session);
		this.fill({
			id: get('id'),
			name: get('username'),
			realname: get('name'),
			email: get('email')
		});
	}
});