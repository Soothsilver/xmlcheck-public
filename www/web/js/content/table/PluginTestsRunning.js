/**
 * Table of currently running (unfinished) plugin tests.
 */
asm.ui.table.PluginTestsRunning = asm.ui.table.PluginTestsBase.extend({
	constructor: function (config) {
		var defaults = {
			filter: function (row) {
				return (row.status == 'running');
			},
			icon: asm.ui.globals.icons.test,
			structure: {
				pluginDescription: { label: asm.lang.pluginTests.pluginDescription, string: true }
			},
			title: asm.lang.pluginTests.runningTests
		};
		this.base($.extend(true, defaults, config));
	}
});