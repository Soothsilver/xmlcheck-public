asm.ui.panel.Changelog = asm.ui.ContentPanel.extend({
	constructor: function (config) {
		var defaults = {
			children: {

			}
		};
        this.base($.extend(defaults, config));
    },
    _buildContent: function() {

        _changelogPanel = this._buildTag('div')
            .append(asm.lang.changelog.loading)
            .appendTo(this.config.target)
            .panel({ icon: 'document'});

	},

    _adjustContent: function () {
        asm.ui.globals.stores.changelog.get(function(data) { _changelogPanel.html(data.changelog); });
    }
});