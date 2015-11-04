/**
 * Table of already rated submissions.
 */
asm.ui.table.CorrectionRated = asm.ui.table.CorrectionBase.extend({
	constructor: function (config) {
		var defaults = {
			icon: asm.ui.globals.icons.submissionRated,
			title:  asm.lang.grading.legacyGradedCaption,
			stores: [asm.ui.globals.stores.correctionRated],
			structure: {
				date: {hidden: true},
                problem: {hidden: false},
				rating: {label: asm.lang.grading.points, comparable: true}
			}
		};
		this.base($.extend(true, defaults, config));
	},
	_showContent: function () {
		var privileges = asm.ui.globals.session.getProperty('privileges') || {};
		if (privileges.submissionsModifyRated) {
			this.config.actions.raw = this.config.actions.raw || [];
			this.config.actions.raw = this.config.actions.raw.concat([
				this._createRateActionConfig(true),
				this._createDownloadSubmissionActionConfig(),
				this._createDownloadOutputActionConfig(),
				this._createSubmissionDetailsActionConfig()
			]);
		}
		this.base.apply(this, arguments);
	}
});