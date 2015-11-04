/**
 * Table of assignments (for owners).
 */
asm.ui.table.Assignments = asm.ui.DynamicTable.extend({
	constructor: function (config) {
		var defaults = {
			icon: asm.ui.globals.icons.assignment,
			structure: {
				id: { key: true, hidden: true, comparable: true },
				problem: { label: asm.lang.assignments.name, comparable: true, string: true },
				deadline: { label: asm.lang.assignments.deadline, comparable: true, string: true },
				reward: { label: asm.lang.assignments.points, comparable: true },
				groupId: { hidden: true, comparable: true },
				group: { label: asm.lang.assignments.group, comparable: true, string: true }
			},
			title: asm.lang.assignments.tutorsAssignments,
			stores: [asm.ui.globals.stores.assignments]
		};
		this.base($.extend(true, defaults, config));
	},
	_adjustContent: function () {
		var groupId = this._params[0] || false;
		if (this._filterId != undefined) {
			this.table('removeFilter', this._filterId);
		}
		if (groupId) {
			this._filterId = this.table('addFilter', 'groupId', 'equal', groupId);
		}
	}
});