/**
 * Table of confirmed submissions.
 */
asm.ui.table.SubmissionsGraded= asm.ui.table.SubmissionsBase.extend({
	constructor: function (config) {
		var defaults = {
            actions: {
                raw: [
                    {
                        icon: 'ui-icon-' + asm.ui.globals.icons.downloadOutput,
                        label: asm.lang.submissions.downloadSubmission,
                        action: function (id)
                        {
                            asm.ui.globals.fileSaver.request('DownloadSubmissionInput',
                                {id: id}, null, $.proxy(this._triggerError, this));
                        }
                    },
                    {
                        icon: 'ui-icon-' + asm.ui.globals.icons.downloadOutput,
                        label: asm.lang.submissions.downloadOutput,
                        filter: function (id, values)
                        {
                            return values['hasOutput'] == 'yes';
                        },
                        action: function (id)
                        {
                            asm.ui.globals.fileSaver.request('DownloadSubmissionOutput',
                                {id: id}, null, $.proxy(this._triggerError, this));
                        }
                    }]
            },
			filter: function (row) {
				return ((row.status == 'graded'));
			},
			icon: asm.ui.globals.icons.submission,
			structure: {
				date: { label: asm.lang.submissions.uploaded, comparable: true, string: true },
				success: { label: asm.lang.submissions.success, comparable: true, renderer: function(percentage) { return percentage + "%"; } },
				rating: { label: asm.lang.submissions.points, comparable: true },
                explanation: { label: asm.lang.submissions.note, string: true}
			},
			title: asm.lang.submissions.gradedSubmissionsCaption
		};
		this.base($.extend(true, defaults, config));
	}
});