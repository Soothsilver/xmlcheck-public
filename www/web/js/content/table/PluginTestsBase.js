/**
 * Base for plugin test tables.
 */
asm.ui.table.PluginTestsBase = asm.ui.DynamicTable.extend({
	constructor: function (config) {
		var defaults = {
			structure: {
				id: { key: true, hidden: true, comparable: true },
				description: { label: asm.lang.pluginTests.description, comparable: true, string: true },
				plugin: { label: asm.lang.pluginTests.plugin, comparable: true, string: true },
				config: { label: asm.lang.pluginTests.config, string: true },
				hasOutput: { hidden: true, renderer: function (value) {
					return (value ? 'yes' : 'no');
				}}
			},
			title: "this title is never actually shown",
			transformer: function (row) {
				var argumentNames = row.pluginArguments ? row.pluginArguments.split(';') : [];
				delete row.pluginArguments;
				var argumentValues = row.arguments ? row.arguments.split(';') : [];
				delete row.arguments;

				var config = [];
				for (var i in argumentNames) {
					if (argumentValues[i] != undefined) {
						config.push(argumentNames[i] + ' = ' + argumentValues[i]);
					}
				}
				row.config = config.join('\n');
				
				return row;
			},
			stores: [asm.ui.globals.stores.pluginTests]
		};
		this.base($.extend(true, defaults, config));
	},
	_buildContent: function () {
		var privileges = asm.ui.globals.session.getProperty('privileges') || {};
		if (privileges.pluginsTest) {
			this.config.actions.extra.push(asm.ui.Macros.trashAction({
				expireOnRemoval: [asm.ui.globals.stores.pluginTests],
				removalRequest: 'DeletePluginTest',
				subject: asm.lang.subjects.pluginTest
			}));
		}
		this.base();
	}
});