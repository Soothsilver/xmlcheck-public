/**
 * User type add/edit form.
 */
asm.ui.form.Usertype = asm.ui.DynamicForm.extend({
	constructor: function (config) {
		var formStructure = { main: {
				icon: asm.ui.globals.icons.usertype,
				caption: asm.lang.usertypes.editCaption,
				fields: {
					id: {
						type: 'hidden'
					},
					name: {
						label: asm.lang.usertypes.name,
						type: 'text',
						hint: asm.lang.usertypes.nameHint,
						check: ['isAlphaNumeric', 'isNotEmpty', asm.ui.Macros.nameCheck('usertypes')]
					}
				}
			}};

		
		$.each(asm.ui.globals.privilegesBreakdown, function (subject, privs) {
			var options = {};
			$.each(privs, function (action, data) {
				options[data[0]] = [data[2], data[1]];
			});
			formStructure.main.fields[subject] = {
				type: 'checkset',
				fancy: true,
				label: function(sub)
                {
                    switch(sub)
                    {
                        case 'users': return asm.lang.usertypes.users;
                        case 'subscriptions': return asm.lang.usertypes.subscriptions;
                        case 'plugins': return asm.lang.usertypes.plugins;
                        case 'assignments': return asm.lang.usertypes.assignments;
                        case 'submissions': return asm.lang.usertypes.correction;
                        case 'lectures': return asm.lang.usertypes.lectures;
                        case 'groups': return asm.lang.usertypes.groups;
                        case 'other': return asm.lang.usertypes.other;
                        default: return sub;
                    }
                }(subject),
				options: options
			};
		});

		var defaults = {
			formStructure: formStructure,
			request: 'EditUsertype'
		};
		this.base($.extend(true, defaults, config));
	}
});