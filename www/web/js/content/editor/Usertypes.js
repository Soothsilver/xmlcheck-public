/**
 * Editor of user types (privileges).
 */
asm.ui.editor.Usertypes = asm.ui.DynamicTableEditor.extend({
	constructor: function (config) {
		var defaults = {
			adjuster: function () {
				this.field('name', 'option', 'editable', false);
			},
			filler: function (id) {
				var usertypes = asm.ui.globals.stores.usertypes.getBy('id', id),
					usertypeData = usertypes[0] || {};

				if (!usertypeData) {
					return false;
				}

				var values = $.extend({}, usertypeData);
				delete values.privileges;
				$.each(asm.ui.globals.privilegesBreakdown, function (subject, privs) {
					var vals = [];
					$.each(privs, function (action, data) {
						if (usertypeData.privileges[data[0]]) {
							vals.push(data[0]);
						}
					});
					values[subject] = vals.join(';');
				});

				this.fill(values);
				
				return true;
			},
			formClass: asm.ui.form.Usertype,
			mainStore: asm.ui.globals.stores.usertypes,
			removalMessage: asm.lang.usertypes.removalMessage,
			removalRequest: 'DeleteUsertype',
			subject: asm.lang.subjects.userType,
			tableClass: asm.ui.table.Usertypes
		};
		this.base($.extend(defaults, config));
	},
	_showContent: function () {
		var privileges = asm.ui.globals.session.getProperty('privileges') || {};
		$.extend(this.config.actions, {
			add: privileges.usersPrivPresets,
			manage: privileges.usersPrivPresets,
			remove: privileges.usersPrivPresets
		});
		this.base();
	}
});