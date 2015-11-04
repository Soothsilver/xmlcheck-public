/**
 * Editor of problems (for teachers).
 */
asm.ui.editor.Problems = asm.ui.DynamicTableEditor.extend({
	constructor: function (config) {
		var defaults = {
			adjuster: function () {
				this.field('lecture', 'option', 'editable', false);
				this.field('name', 'option', 'editable', false);
				this.form('getFieldByName', 'plugin').change();
			},
			expireOnRemoval: [
				asm.ui.globals.stores.assignments,
				asm.ui.globals.stores.submissions,
				asm.ui.globals.stores.correction,
				asm.ui.globals.stores.correctionRated
			],
			formClass: asm.ui.form.Problem,
			mainStore: asm.ui.globals.stores.problems,
			removalRequest: 'DeleteProblem',
			subject: asm.lang.subjects.problem,
			tableClass: asm.ui.table.Problems
		};
		this.base($.extend(defaults, config));
	},
	_showContent: function () {
		var privileges = asm.ui.globals.session.getProperty('privileges') || {},
			privManageProblems = privileges.lecturesManageOwn || privileges.lecturesManageAll;
		$.extend(this.config.actions, {
			add: privManageProblems,
			manage: privManageProblems,
			remove: privManageProblems
		});
		this.base();
	}
});