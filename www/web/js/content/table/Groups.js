/**
 * Table of assignment groups.
 */
asm.ui.table.Groups = asm.ui.DynamicTable.extend({
	constructor: function (config) {
		var defaults = {
			actions: {
				raw: [{
					icon: 'ui-icon-' + asm.ui.globals.icons.assignment,
					label: asm.lang.groups.showAssignments,
					action: $.proxy(function (id) {
						this.trigger('groups.showAssignments', { groupId: id });
					}, this)
				}, {
					icon: 'ui-icon-' + asm.ui.globals.icons.user,
					label: asm.lang.groups.showRatings,
					action: $.proxy(function (id) {
						this.trigger('groups.showRatings', { groupId: id });
					}, this)
				}]
			},
			icon: asm.ui.globals.icons.group,
			structure: {
				id: { key: true, hidden: true, comparable: true },
				name: { label: asm.lang.groups.name, comparable: true, string: true },
				description: { label: asm.lang.groups.description, string: true },
				type: { label: asm.lang.groups.type, comparable: true },
				lecture: { label: asm.lang.groups.lecture, comparable: true, string: true }
			},
			title: asm.lang.groups.caption,
			stores: [asm.ui.globals.stores.groups]
		};
		this.base($.extend(true, defaults, config));
	}
});