/**
 * Table of currently pending assignments for user.
 */
asm.ui.table.StudentAssignmentsCurrent = asm.ui.table.StudentAssignments.extend({
	constructor: function (config) {
		var defaults = {
			filter: function (row) {
				return (row.deadline >= asm.ui.TimeUtils.mysqlTimestamp());
			},
			title: asm.lang.assignments.currentAssignments
		};
		this.base($.extend(true, defaults, config));
	}
});