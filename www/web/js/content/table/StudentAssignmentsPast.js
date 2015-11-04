/**
 * Table of assignments for user that are past due.
 */
asm.ui.table.StudentAssignmentsPast = asm.ui.table.StudentAssignments.extend({
	constructor: function (config) {
		var defaults = {
			filter: function (row) {
				return (row.deadline < asm.ui.TimeUtils.mysqlTimestamp());
			},
			title: asm.lang.assignments.pastAssignments
		};
		this.base($.extend(true, defaults, config));
	}
});