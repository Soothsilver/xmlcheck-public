/**
 * Base for tables of assignments for user.
 */
asm.ui.table.StudentAssignments = asm.ui.DynamicTable.extend({
	constructor: function (config) {
		var defaults = {
			actions: {
				raw: [{
					icon: 'ui-icon-' + asm.ui.globals.icons.problem,
					label: asm.lang.assignments.openAssignment,
					action: $.proxy(function (id) {
						this.trigger('studentAssignments.openAssignment', { assignmentId: id });
					}, this)
				}]
			},
			icon: asm.ui.globals.icons.assignment,
			structure: {
				id: { key: true, hidden: true, comparable: true },
				name: { label: asm.lang.assignments.name, comparable: true, string: true },
				deadline: { label: asm.lang.assignments.deadline, comparable: true, string: true },
				reward: { label: asm.lang.assignments.points, comparable: true },
				lecture: { label: asm.lang.assignments.lecture, comparable: true, string: true },
				group: { label: asm.lang.assignments.group, comparable: true, string: true },
				submissionExists: { label: asm.lang.assignments.somethingSubmitted, renderer: function (value) {
					return (value) ? asm.lang.assignments.somethingSubmittedYes : '';
				}}
			},
			title: 'irrelevant title (this will never be constructed)',
			stores: [asm.ui.globals.stores.studentAssignments]
		};
		this.base($.extend(true, defaults, config));
	},
	_initContent: function () {
		this.base();
		this.table('sort', 'deadline');
	}
});