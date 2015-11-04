/**
 * Editor of lectures (for teachers).
 */
asm.ui.editor.Lectures = asm.ui.DynamicTableEditor.extend({
	constructor: function (config) {
		var defaults = {
			adjuster: function () {
				this.field('name', 'option', 'editable', false);
			},
			expireOnRemoval: [
				asm.ui.globals.stores.problems,
				asm.ui.globals.stores.problemsLite,
				asm.ui.globals.stores.groups,
				asm.ui.globals.stores.subscriptions,
				asm.ui.globals.stores.assignments,
				asm.ui.globals.stores.submissions,
				asm.ui.globals.stores.correction,
				asm.ui.globals.stores.correctionRated
			],
			formClass: asm.ui.form.Lecture,
			mainStore: asm.ui.globals.stores.lectures,
			removalRequest: 'DeleteLecture',
			subject: asm.lang.subjects.lecture,
			tableClass: asm.ui.table.Lectures
		};
		this.base($.extend(defaults, config));
	},
	_showContent: function () {
		var privileges = asm.ui.globals.session.getProperty('privileges') || {},
			privManageLectures = privileges.lecturesManageOwn || privileges.lecturesManageAll;
		$.extend(this.config.actions, {
			add: privileges.lecturesAdd,
			manage: privManageLectures,
			remove: privManageLectures
		});
		this.base.apply(this, arguments);
	}
});