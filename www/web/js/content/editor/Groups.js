/**
 * Editor of groups.
 */
asm.ui.editor.Groups = asm.ui.DynamicTableEditor.extend({
	constructor: function (config) {
		var defaults = {
			adjuster: function () {
				this.field('lecture', 'option', 'editable', false);
				this.field('name', 'option', 'editable', false);
			},
			expireOnRemoval: [
				asm.ui.globals.stores.availableGroups,
				asm.ui.globals.stores.subscriptions,
				asm.ui.globals.stores.assignments,
				asm.ui.globals.stores.submissions,
				asm.ui.globals.stores.correction,
				asm.ui.globals.stores.correctionRated
			],
			filler: function (id) {
				var groups = asm.ui.globals.stores.groups.getBy('id', id);

				if (!groups.length) {
					return false;
				}
				
				this.fill($.extend({}, groups[0], {
					lecture: groups[0].lectureId,
					'public': (groups[0].type == 'public')
				}));

				return true;
			},
			formClass: asm.ui.form.Group,
			mainStore: asm.ui.globals.stores.groups,
			removalRequest: 'DeleteGroup',
			subject: asm.lang.subjects.group,
			tableClass: asm.ui.table.Groups
		};
		this.base($.extend(defaults, config));
	},
	_showContent: function () {
		var privileges = asm.ui.globals.session.getProperty('privileges') || {},
			privManageGroups = privileges.groupsManageOwn || privileges.groupsManageAll;
		$.extend(this.config.actions, {
			add: privileges.groupsAdd,
			manage: privManageGroups,
			remove: privManageGroups
		});
		this.base();
	}
});