/**
 * Table of corrected and yet unconfirmed submissions.
 */
asm.ui.table.SubmissionsNormal = asm.ui.table.SubmissionsBase.extend({
	constructor: function (config) {
		var defaults = {
			actions: {
				extra: [
                    // Hands-off
                    {
                        callback: $.proxy(function () {
                            this.trigger('custom.confirmSubmission');
                        }, this),
                        confirmText: asm.lang.submissions.handsOffWarning,
                        confirmTitle: asm.lang.submissions.handsOffCaption,
                        expire: [
                            asm.ui.globals.stores.submissions
                        ],
                        filter: function (id, values) {
                            return values['status'] != asm.lang.submissions.statusRequestingGrading;
                        },
                        icon: 'ui-icon-' + asm.ui.globals.icons.confirm,
                        label: asm.lang.submissions.handsOff,
                        refresh: true,
                        request: 'HandOffSubmission'
                    },
                    asm.ui.Macros.trashAction({
                        subject: asm.lang.subjects.submission,
                        removalRequest: 'DeleteSubmission',
                        expireOnRemoval: [asm.ui.globals.stores.submissions]
                    })],
				raw:  [
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
				return (row.status != 'new' && row.status != 'graded');
			},
			structure: {
				deadline: { label: asm.lang.submissions.deadline, comparable: true, string: true },
				success: { label: asm.lang.submissions.success, comparable: true, renderer: function(percentage) { return percentage + "%"; } },
                status : { label: asm.lang.submissions.status, comparable: true, renderer: function(dbstatus)
                {
                    switch (dbstatus)
                    {
                        case 'handsoff': return asm.lang.submissions.statusRequestingGrading;
                        case 'normal': return asm.lang.submissions.statusNormal;
                        case 'latest': return asm.lang.submissions.statusLatest;
                        case 'graded': return asm.lang.submissions.statusGraded;
                        default : return 'old file: ' + dbstatus;
                    }
                }},
                date: { label: asm.lang.submissions.uploaded, comparable: true, string: true },
				details: { label: asm.lang.submissions.details, string: true, renderer: asm.ui.StringUtils.htmlspecialchars }
			},
			title: asm.lang.submissions.yourSubmissionsCaption
		};
		this.base($.extend(true, defaults, config));
	},
    _initContent: function () {
        this.base();
        this.table('sort', 'date');
    }
});