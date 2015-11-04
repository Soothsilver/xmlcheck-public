/**
 * Editor of users.
 */
asm.ui.editor.Users = asm.ui.DynamicTableEditor.extend({
	constructor: function (config) {
		var defaults = {
			adjuster: function () {
				this.field('name', 'option', 'editable', false);
			},
			filler: function (id) {
				var users = asm.ui.globals.stores.users.getBy('id', id),
					userData = users[0] || null;

				if (!userData) {
					return false;
				}

				this.fill({
					id: userData.id,
					name: userData.username,
					type: userData.typeId,
					realname: userData.name,
					email: userData.email
				});

				return true;
			},
			formClass: asm.ui.form.User,
			mainStore: asm.ui.globals.stores.users,
			removalRequest: 'DeleteUser',
			subject: asm.lang.subjects.user,
			tableClass: asm.ui.table.Users
		};
		this.base($.extend(defaults, config));
	},
	_showContent: function () {
		var privileges = asm.ui.globals.session.getProperty('privileges') || {};
		$.extend(this.config.actions, {
			add: privileges.usersAdd,
			manage: privileges.usersManage,
			remove: privileges.usersRemove
		});
		this.base();
	}
});