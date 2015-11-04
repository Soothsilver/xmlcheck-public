/**
 * Container with two tables: submissions ready to be rated and already rated submissions.
 */
asm.ui.panel.Correction = asm.ui.Container.extend({
	constructor: function (config) {
		var defaults = {
			children: {
				fresh: new asm.ui.table.CorrectionNew(),
				rated: new asm.ui.table.CorrectionRated()
			}
		};
		this.base($.extend(true, defaults, config));

		this.config.children.fresh.bind('panel.refresh', function () {
			this.config.children.rated.refresh();
		}, this);
	},
	_adjustContent: function () {
        if (this._params[0] === 'submission')
        {
            var submissionId = this._params[1] || false;
            var children = this.config.children;
            if (this._filterIds != undefined)
            {
                children.fresh.table('removeFilter', this._filterIds.fresh);
                children.rated.table('removeFilter', this._filterIds.rated);
            }
            if (submissionId)
            {
                this._filterIds = {
                    fresh: children.fresh.table('addFilter', 'id', 'equal', submissionId),
                    rated: children.rated.table('addFilter', 'id', 'equal', submissionId)
                };
            }
        }
        else
        {
            var authorId = this._params[0] || false;
            var children = this.config.children;
            if (this._filterIds != undefined)
            {
                children.fresh.table('removeFilter', this._filterIds.fresh);
                children.rated.table('removeFilter', this._filterIds.rated);
            }
            if (authorId)
            {
                this._filterIds = {
                    fresh: children.fresh.table('addFilter', 'authorId', 'equal', authorId),
                    rated: children.rated.table('addFilter', 'authorId', 'equal', authorId)
                };
            }
        }
	}
});