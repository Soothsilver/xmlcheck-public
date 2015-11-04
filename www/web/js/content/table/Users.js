/**
 * Table of users.
 */
asm.ui.table.Users = asm.ui.DynamicTable.extend({
	constructor: function (config) {
		var defaults = {
			icon: asm.ui.globals.icons.user,
			structure: {
				id: { key: true, hidden: true, comparable: true },
				username: { label: asm.lang.users.username, comparable: true, string: true },
				typeId: { hidden: true },
				type: { label: asm.lang.users.type, comparable: true, string: true },
				name: { label: asm.lang.users.realName, comparable: true, string: true },
				email: { label: asm.lang.users.email, comparable: true, string: true },
				lastLogin: { label: asm.lang.users.lastLogin, comparable: true, string: true }
			},
			title: asm.lang.users.caption,
			stores: [asm.ui.globals.stores.users]
		};
		this.base($.extend(true, defaults, config));
	}
});