/**
 * Special container for managing pending assignments and submitting solutions.
 * Contains two exclusive sets of content - tables and forms. Tables are two,
 * current and past assignments. "Forms" are the following panels: non-editable
 * "form" with assignment details, form for adding new submission. Submission
 * for may be substituted with message panel if browsing details of assignments
 * that are past due or already submitted.
 */
asm.ui.panel.Assignments = asm.ui.Container.extend({


	constructor: function (config) {
		var defaults = {
			children: {
				tableCurrent: new asm.ui.table.StudentAssignmentsCurrent(),
				tablePast: new asm.ui.table.StudentAssignmentsPast(),
				details: new asm.ui.form.AssignmentDetails({
					stores: [asm.ui.globals.stores.studentAssignments]
				}).extend({
					_adjustContent: function () {
						var assignments = asm.ui.globals.stores.studentAssignments.getBy('id', this._params[0]),
							assignmentData = assignments[0] || null;


                        if (!assignmentData)
                        {
                            this._requestAdjust();
                            return;
                        }
                        this._parent._displaySubmissionForm(assignmentData.id,
                            assignmentData.submissionExists,
                            assignmentData.submissionGraded,
                            assignmentData.deadline < asm.ui.TimeUtils.mysqlTimestamp()
                        );
                        this.fill(assignmentData);



/*
						if (!assignmentData) {
                            // The ID in hash-url does not exist.
							this.trigger('custom.assignmentState', { state: null });
							return;
						}

						if (assignmentData.submissionGraded) {
							this.trigger('custom.assignmentState', { state: 'graded' });
						} else if (assignmentData.deadline < asm.ui.TimeUtils.mysqlTimestamp()) {
							this.trigger('custom.assignmentState', {
                                state: 'missed',
                                id: assignmentData.id
                            });
						} else {
							this.trigger('custom.assignmentState', {
								state: 'ok',
								id: assignmentData.id
							});
						}
						this.fill(assignmentData);
*/
					}
				}),
                noticePanel: new asm.ui.ContentPanel().extend({
                    _buildContent: function () {
                        var panel = $('<div></div>')
                            .appendTo(this.config.target)
                            .panel({ icon: 'info' });
                        this._noticeEl = $('<span></span>')
                            .appendTo(panel);
                    },
                    _adjustContent: function () {
                        this._noticeEl.html(this._params[0]);
                    }
                }),
                submissionForm: new asm.ui.form.Submission(),
			}
		};
		this.base($.extend(defaults, config));

		var onAssignmentOpen = $.proxy(function (params) {
			this._requestAdjust([params.assignmentId])
		}, this);

		this.config.children.tableCurrent.bind('studentAssignments.openAssignment', onAssignmentOpen);
		this.config.children.tablePast.bind('studentAssignments.openAssignment', onAssignmentOpen);

		this.config.children.submissionForm.bind('form.success', function () {
			asm.ui.globals.stores.submissions.expire();
			this.trigger('assignments.showSubmissions');
		}, this);

		this.config.children.details.bind('custom.assignmentState', function (params, event) {
			event.stopPropagation();
			var children = this.config.children;
			switch (params.state) {
				case 'ok':
					children.submissionForm.show();
					children.submissionForm.field('assignmentId', 'option', 'value', params.id);
                    children.noticePanel.show(['Your submission is NOT automatically confirmed when you add it. You must confirm it manually in the "Corrected submission drafts" table.']);
					break;
				case 'submitted':
					children.noticePanel.show(['You have already confirmed a solution for this assignment. You cannot add or confirm solutions for this assignment anymore.']);
					break;
				case 'missed':
                    children.submissionForm.show();
                    children.submissionForm.field('assignmentId', 'option', 'value', params.id);
					children.noticePanel.show(['The assignment is past deadline. You may be penalized for sending your solution late.']);
					break;
				default:
					this._requestAdjust();
			}
		}, this);
	},
    _displaySubmissionForm: function (id, submissionExists, submissionGraded, deadlineMissed)
    {
        var children = this.config.children;
        children.submissionForm.show();
        children.submissionForm.field('assignmentId', 'option', 'value', id);
        var text = submissionGraded ? asm.lang.assignments.addSubmissionButAlreadyGradedMessage :
            (submissionExists ? asm.lang.assignments.addSubmissionButAlreadyExistsMessage :
                asm.lang.assignments.addSubmissionFirstTimeMessage);
        if (deadlineMissed)
        {
            text = text + "<br />" + asm.lang.assignments.addSubmissionAfterDeadline;
        }
        children.noticePanel.show(
            [text]);
    },
	_showContent: function () {
	},
	_adjustContent: function () {
		this._callOnChildren('hide');

		var assignmentId = this._params[0],
			children = this.config.children;
		if (assignmentId) {
			children.details.show([assignmentId]);
		} else {
			children.tableCurrent.show();
			children.tablePast.show();
		}
	}
});