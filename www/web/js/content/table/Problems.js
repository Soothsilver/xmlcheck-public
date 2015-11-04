/**
 * Table of problems.
 */
asm.ui.table.Problems = asm.ui.DynamicTable.extend({
	constructor: function (config) {
		var defaults = {
			icon: asm.ui.globals.icons.problem,
			structure: {
				id: { key: true, hidden: true, comparable: true },
				name: { label: asm.lang.problems.name, comparable: true, string: true },
				description: { label: asm.lang.problems.description, string: true },
				lectureId: { hidden: true, comparable: true },
				lecture: { label: asm.lang.problems.lecture, comparable: true, string: true }
			},
			title: asm.lang.problems.caption,
			stores: [asm.ui.globals.stores.problems]
		};
		this.base($.extend(true, defaults, config));
	},
	_adjustContent: function () {
		var lectureId = this._params[0] || false;
		if (this._filterId != undefined) {
			this.table('removeFilter', this._filterId);
		}
		if (lectureId) {
			this._filterId = this.table('addFilter', 'lectureId', 'equal', lectureId);
		}
	}
});