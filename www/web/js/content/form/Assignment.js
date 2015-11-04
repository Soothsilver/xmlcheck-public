/**
 * Add/edit assignment form.
 */
asm.ui.form.Assignment = asm.ui.DynamicForm.extend({
	constructor: function (config) {
		var defaults = {
			formStructure: { main: {
				icon: asm.ui.globals.icons.assignment,
				caption: asm.lang.assignments.addEditAssignmentCaption,
				fields: {
					id: {
						type: 'hidden'
					},
					group: {
						label: asm.lang.assignments.group,
						type: 'select',
						check: 'isNotEmpty'
					},
					problem: {
						label: asm.lang.assignments.problem,
						type: 'select',
						check: 'isNotEmpty'
					},
					deadline: {
						label: asm.lang.assignments.deadline,
						type: 'date',
						check: 'isDate'
					},
					reward: {
						label: asm.lang.assignments.rewardEdit,
						type: 'text',
						hint: asm.lang.assignments.rewardHint,
						check: 'isNonNegativeNumber'
					}
				}
			}},
			request: 'EditAssignment',
			stores: [asm.ui.globals.stores.groups, asm.ui.globals.stores.problemsLite]
		};
		if (config && config.stores) {
			$.merge(defaults.stores, config.stores);
			delete config.stores;
		}
		this.base($.extend(true, defaults, config));

	},
	_initContent: function () {
		var groups = asm.ui.globals.stores.groups.get(),
			problems = asm.ui.globals.stores.problemsLite.get(),
			groupSelect = this.form('getFieldByName', 'group'),
			problemSelect = this.form('getFieldByName', 'problem'),
			groupProblems = {};

		for (var i in groups) {
			groupProblems[groups[i].id] = {};
			var noProblems = true;
			for (var j in problems) {
				if (groups[i].lectureId == problems[j].lectureId) {
					noProblems = false;
					groupProblems[groups[i].id][problems[j].id] = problems[j].name;
				}
			}
			if (noProblems) {
				groupProblems[groups[i].id] = null;
			}
		}
		groups = $.grep(groups, function (group) {
			return (groupProblems[group.id] != null);
		});

		this.setFieldOptions('group', asm.ui.Utils.tableToOptions(groups, 'id', 'name'));

		groupSelect.unbind('change.formInit').bind('change.formInit', function () {
			problemSelect.field('option', 'options', groupProblems[groupSelect.field('option', 'value')])
		}).change();
	}
});