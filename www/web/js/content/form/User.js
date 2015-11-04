/**
 * Add/edit user form.
 */
asm.ui.form.User = asm.ui.DynamicForm.extend({
	constructor: function (config) {
		var defaults = {
			formStructure: { main: {
				icon: asm.ui.globals.icons.user,
				caption: asm.lang.users.editCaption,
				fields: {
					id: {
						type: 'hidden'
					},
					name: {
						label: asm.lang.users.username,
						type: 'text',
						hint: asm.lang.users.usernameHint,
						check: ['isAlphaNumeric', 'hasLength', asm.ui.Macros.nameCheck('users')],
						checkParams: { minLength: asm.ui.constants.usernameMinLength, maxLength: asm.ui.constants.usernameMaxLength }
					},
					type: {
						label: asm.lang.users.type,
						type: 'select',
						hint: asm.lang.users.typeHint,
						check: 'isNotEmpty'
					},
					realname: {
						label: asm.lang.users.realName,
						type: 'text',
						hint: asm.lang.users.realNameHint,
						check: ['isNotEmpty', 'isName']
					},
					email: {
						label: asm.lang.users.email,
						type: 'text',
						check: 'isEmail'
					},
					pass: {
						label: asm.lang.users.password,
						type: 'password',
                        hint: asm.lang.users.passwordHint,
                        check: function(value, field) {
                            if (value.length == 0) { return false; }
                            if (value.length < asm.ui.constants.passwordMinLength) { return asm.lang.users.passwordTooShort; }
                            if (value.length > asm.ui.constants.passwordMaxLength) { return asm.lang.users.passwordTooLong; }
                            return false;
                        }
					},
					repass: {
						name: 'repass',
						label: asm.lang.users.retypePassword,
						type: 'password',
						hint: asm.lang.users.retypePasswordHint,
						check: function (value, field) {
							return (value != field.prev().field('option', 'value'))
								? asm.lang.users.passwordRetypeError : false;
						}
					}
				}
			}},
			request: 'EditUser',
			stores: [asm.ui.globals.stores.usertypes]
		};
		if (config && config.stores) {
			$.merge(defaults.stores, config.stores);
			delete config.stores;
		}
		this.base($.extend(true, defaults, config));
	},
	_initContent: function () {
		this.setFieldOptions('type', asm.ui.Utils.tableToOptions(asm.ui.globals.stores.usertypes.get(), 'id', 'name'));
	}
});