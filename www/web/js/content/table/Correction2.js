/**
 * Table of submissions awaiting correction.
 */
asm.ui.table.Correction2 = asm.ui.table.CorrectionBase.extend({
	constructor: function (config) {
		var defaults = {
			actions: {
				raw: [
					this._createRateActionConfig(),
					this._createDownloadSubmissionActionConfig(),
					this._createDownloadOutputActionConfig(),
					this._createSubmissionDetailsActionConfig()
				]
			},
			title:  'this title will never be displayed',
			stores: [asm.ui.globals.stores.correctionAll]
		};
		this.base($.extend(true, defaults, config));
	},
    _adjustContent: function() {
    }
});