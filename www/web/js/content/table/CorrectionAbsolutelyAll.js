/**
 * Table of submissions awaiting correction.
 */
asm.ui.table.CorrectionAbsolutelyAll = asm.ui.table.CorrectionBase.extend({
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
            structure: {
                problem: { hidden: false }
            },
            title:  asm.lang.grading.absolutelyAllCaption,
            stores: [asm.ui.globals.stores.correctionAbsolutelyAll]
        };
        this.base($.extend(true, defaults, config));
    },
    _adjustContent: function () {
        var authorId = this._params[0] || false;
        if (this._filterIds != undefined) {
            this.table('removeFilter', this._filterIds.one);
        }
        if (authorId) {
            this._filterIds = {
                one: this.table('addFilter', 'authorId', 'equal', authorId)
            };
        }
    }
});