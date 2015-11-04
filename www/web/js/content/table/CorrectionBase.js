/**
 * Base class for table of submissions for correction (or corrected).
 */
asm.ui.table.CorrectionBase = asm.ui.DynamicTable.extend({
	constructor: function (config) {
		var defaults = {
			actions: {
				raw: []
			},
			icon: asm.ui.globals.icons.submission,
			structure: {
				id: {key: true, hidden: true, comparable: true},
				problem: {label: asm.lang.grading.problem, hidden: true, comparable: true, string: true},
				group: {label: asm.lang.grading.group, comparable: true, string: true},
				date: {label: asm.lang.grading.uploaded, comparable: true, string: true},
				fulfillment: {label: asm.lang.grading.fulfillment, renderer: function(percentage) { return percentage + "%"; }},
				details: {label: asm.lang.grading.details, string: true},
				reward: {hidden: true, comparable: true},
				authorId: {hidden: true, comparable: true},
				author: {label: asm.lang.grading.author, hidden: true, comparable: true, string: true},
                deadline: { hidden: true, comparable: true, string: true},
                explanation: {hidden: true, string: true},
                hasOutput: {hidden: true, renderer: function (value) {
                    return (value ? 'yes' : 'no');
                }}
			}
		};
		this.base($.extend(true, defaults, config));
	},
	_showContent: function () {
		var privileges = asm.ui.globals.session.getProperty('privileges') || {};
		this.config.structure.author.hidden = !privileges.submissionsViewAuthors;
		this.base.apply(this, arguments);
	},
	_createRateActionConfig: function (reRate) {
		var triggerError = $.proxy(this._triggerError, this);
		return {
			icon: 'ui-icon-' + asm.ui.globals.icons[reRate ? 'edit' : 'rating'],
			label: reRate ? asm.lang.grading.regradeSubmission : asm.lang.grading.gradeSubmission,
			action: $.proxy(function (id, values) {
				var options = [],
					maxRating = values['reward'],
					fulfillment = values['fulfillment'],
                    submissionDate = values['date'],
                    deadlineDate = values['deadline'],
					rating = values['rating'],
                    explanation = values['explanation'];

				for (var i = 0; i <= maxRating; ++i) {
					options.push(i);
				}
                var late = submissionDate > deadlineDate;

                    asm.ui.globals.dialogManager.form($.proxy(function (data) {
                        asm.ui.globals.coreCommunicator.request('RateSubmission', data, $.proxy(function () {
                            asm.ui.globals.stores.ratingsTeacherDetailed.expire();
							asm.ui.globals.stores.correctionRated.expire();
							asm.ui.globals.stores.correction.expire();
							asm.ui.globals.stores.correctionAll.expire();
							asm.ui.globals.stores.correctionAbsolutelyAll.expire();
                            this.refresh(true);
                        }, this), $.noop, triggerError);
                    }, this), {
                        fields: {
                            id: {
                                type: 'hidden',
                                value: id
                            },
                            rating: {
                                type: 'select',
                                options: options,
                                label: asm.lang.grading.rating,
                                value: reRate ? rating : maxRating
                            },
                            explanation: {
                               type: 'textarea',
                               label: asm.lang.grading.noteToStudent,
                               value: explanation
                            },
                            lateNotice: {
                                type: (late ? 'info' : 'hidden'),
                                label: asm.lang.grading.submittedLate,
                                value:
                                 asm.lang.grading.submittedLateHint_1 + deadlineDate +
                                 asm.lang.grading.submittedLateHint_2 + submissionDate +
                                 asm.lang.grading.submittedLateHint_3
                            }
                        },
                        submitText: reRate ? asm.lang.grading.changeButton : asm.lang.grading.rateButton
                    }, (reRate ? asm.lang.grading.changeSubmissionRatingCaption: asm.lang.grading.gradeSubmissionCaption)  );


			}, this)
		};
	},
	_createDownloadSubmissionActionConfig: function () {
		var triggerError = $.proxy(this._triggerError, this);
		return {
			icon: 'ui-icon-' + asm.ui.globals.icons.downloadInput,
			label: asm.lang.grading.downloadSubmission,
			action: function (id) {
				asm.ui.globals.fileSaver.request('DownloadSubmissionInput',
						{id: id}, null, triggerError);
			}
		};
	},
	_createDownloadOutputActionConfig: function () {
		var triggerError = $.proxy(this._triggerError, this);
		return {
			icon: 'ui-icon-' + asm.ui.globals.icons.downloadOutput,
			label: asm.lang.grading.downloadOutput,
            filter: function(id, values)
            {
              return values['hasOutput'] == "yes";
            },
			action: function (id) {
				asm.ui.globals.fileSaver.request('DownloadSubmissionOutput',
						{id: id}, null, triggerError);
			}
		};
	},
	_createSubmissionDetailsActionConfig: function () {
		var triggerError = $.proxy(this._triggerError, this);
		return {
			icon: 'ui-icon-' + asm.ui.globals.icons.submissionDetails,
			label: asm.lang.grading.getPlagiarismInfo,
			action: $.proxy(function (id) {
				this.trigger('goToSubmissionDetails', { newId : id } );
			}, this)
		};
	}
});