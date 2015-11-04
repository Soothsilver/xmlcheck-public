/**
 * Add submission form.
 */
asm.ui.form.Submission = asm.ui.DynamicForm.extend({
	constructor: function (config) {
		var defaults = {
			formStructure: { main: {
				icon: asm.ui.globals.icons.submission,
				caption: asm.lang.submissions.addSubmissionCaption,
				fields: {
					assignmentId: {
						type: 'hidden'
					},
					submission: {
						label: asm.lang.submissions.submissionFile,
						type: 'file',
						hint: asm.lang.submissions.submissionFileHint,
						check: ['hasExtension'],
						checkParams: { extensions: ['zip'] }
					}
				}
			}},
			request: 'AddSubmission'
		};
		this.base($.extend(true, defaults, config));
	}
});