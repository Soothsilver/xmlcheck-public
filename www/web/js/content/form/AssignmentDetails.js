/**
 * Pseudo-form with assignment details (non-editable).
 */
asm.ui.form.AssignmentDetails = asm.ui.DynamicForm.extend({
	constructor: function (config) {
		var defaults = {
			formProps: {
				offline: true
			},
			formStructure: { main: {
				icon: asm.ui.globals.icons.problem,
				caption: asm.lang.assignmentDetails.caption,
				fields: {
					name: {
						label: asm.lang.assignmentDetails.problemName,
						type: 'info'
					},
					deadline: {
						label: asm.lang.assignmentDetails.deadline,
						type: 'info'
					},
					reward: {
						label: asm.lang.assignmentDetails.reward,
						type: 'info'
					},
					lecture: {
						label: asm.lang.assignmentDetails.lecture,
						type: 'info'
					},
					group: {
						label: asm.lang.assignmentDetails.group,
						type: 'info'
					},
					description: {
						label: asm.lang.assignmentDetails.problemDescription,
						type: 'info'
					},
					pluginDescription: {
						label: asm.lang.assignmentDetails.pluginDescription,
						type: 'info'
					}
				}
			}}
		};
		this.base($.extend(true, defaults, config));
	}
});