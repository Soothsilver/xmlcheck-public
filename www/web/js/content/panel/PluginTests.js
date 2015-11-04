/**
 * Container with two tables - running and completed tests - and a new test form.
 */
asm.ui.panel.PluginTests = asm.ui.Container.extend({
	constructor: function (config) {
		var defaults = {
			children: {
				tableRunning: new asm.ui.table.PluginTestsRunning(),
				tableCompleted: new asm.ui.table.PluginTestsCompleted(),
				formAdd: new asm.ui.form.PluginTest()
			}
		};
		this.base($.extend(defaults, config));

		var children = this.config.children;
		var store = asm.ui.globals.stores.pluginTests;
		var refreshTables = $.proxy(function thisFn () {
			thisFn.revision = store.getRevision();
			children.tableRunning.refresh(true);
			children.tableCompleted.refresh();
			window.setTimeout($.proxy(function () {
				if (store.getRevision() != thisFn.revision) {
					thisFn.call(this);
				}
			}, this), 5 * 1000);
		}, this);
		children.formAdd.bind('form.success', refreshTables);
	}
});