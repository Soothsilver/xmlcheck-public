/**
 * Base for submission tables.
 */
asm.ui.table.SubmissionsBase = asm.ui.DynamicTable.extend({
	constructor: function (config) {
		var defaults = {
			structure: {
				id: { key: true, hidden: true, comparable: true },
				hasOutput: { hidden: true, renderer: function (value) {
					return (value ? 'yes' : 'no');
				}},
				problem: { label: asm.lang.submissions.problem, comparable: true, string: true }
			},
			icon: asm.ui.globals.icons.submissionDraft,
			stores: [asm.ui.globals.stores.submissions]
		};
		this.base($.extend(true, defaults, config));
	}
});