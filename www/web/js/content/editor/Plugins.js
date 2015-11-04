/**
 * Editor of plugins.
 */
asm.ui.editor.Plugins = asm.ui.DynamicTableEditor.extend({
	constructor: function (config) {
		var defaults = {
			expireOnRemoval: [asm.ui.globals.stores.pluginTests],
			formClass: asm.ui.form.Plugin,
			mainStore: asm.ui.globals.stores.plugins,
			removalRequest: 'DeletePlugin',
			subject: asm.lang.subjects.plugin,
			tableClass: asm.ui.table.Plugins
		};
		this.base($.extend(defaults, config));
	},
	_showContent: function () {
		var privileges = asm.ui.globals.session.getProperty('privileges') || {};
		$.extend(this.config.actions, {
			add: privileges.pluginsAdd,
			remove: privileges.pluginsRemove
		});
		this.base();
	}
});