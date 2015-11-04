/**
 * Table of plugin test results.
 */
asm.ui.table.PluginTestsCompleted = asm.ui.table.PluginTestsBase.extend({
	constructor: function (config) {
		var triggerError = $.proxy(this._triggerError, this);
		var defaults = {
			actions: {
				raw: [{
					icon: 'ui-icon-' + asm.ui.globals.icons.downloadInput,
					label: asm.lang.pluginTests.downloadInput,
					action: function (id) {
						asm.ui.globals.fileSaver.request('DownloadPluginTestInput', { id: id }, null, triggerError);
					}
				}, {
					icon: 'ui-icon-' + asm.ui.globals.icons.downloadOutput,
					label: asm.lang.pluginTests.downloadOutput,
					filter: function (id, values)
					{
						return values['hasOutput'] == 'yes';
					},
					action: function (id) {
						asm.ui.globals.fileSaver.request('DownloadPluginTestOutput', { id: id }, null, triggerError);
					}
				}]
			},
			filter: function (row) {
				return (row.status != 'running');
			},
			icon: asm.ui.globals.icons.results,
			structure: {
				fulfillment: { label: '%', comparable: true },
				details: { label: asm.lang.pluginTests.details, string: true, renderer: asm.ui.StringUtils.htmlspecialchars }
			},
			title: asm.lang.pluginTests.completedTests
		};
		this.base($.extend(true, defaults, config));
	}
});