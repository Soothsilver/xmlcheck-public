/**
 * Editor of assignments (for group owners).
 */
asm.ui.editor.Assignments = asm.ui.DynamicTableEditor.extend({
	constructor: function (config) {
		var defaults = {
			adjuster: function () {
				this.field('group', 'option', 'editable', false);
				this.field('problem', 'option', 'editable', false);
				this.form('getFieldByName', 'group').change();
			},
			expireOnRemoval: [
				asm.ui.globals.stores.submissions,
				asm.ui.globals.stores.correction,
				asm.ui.globals.stores.correctionRated
			],
			formClass: asm.ui.form.Assignment,
			mainStore: asm.ui.globals.stores.assignments,
			removalRequest: 'DeleteAssignment',
			subject: asm.lang.subjects.assignment,
			tableClass: asm.ui.table.Assignments
		};
		this.base($.extend(defaults, config));
	},
	_showContent: function () {
		var privileges = asm.ui.globals.session.getProperty('privileges') || {},
			privManageAssignments = privileges.groupsManageOwn || privileges.groupsManageAll;
		$.extend(this.config.actions, {
			add: privManageAssignments,
			manage: privManageAssignments,
			remove: privManageAssignments
		});
		this.base();
	}
});