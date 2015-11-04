/**
 * Form for launching plugin tests.
 */
asm.ui.form.PluginTest = asm.ui.DynamicForm.extend({
	constructor: function (config) {
		var defaults = {
			formStructure: { main: {
				icon: asm.ui.globals.icons.test,
				caption: asm.lang.pluginTests.runNewTestCaption,
				fields: {
					description: {
						label: asm.lang.pluginTests.description,
						type: 'text',
						check: 'isNotEmpty'
					},
					plugin: {
						label: asm.lang.pluginTests.plugin,
						type: 'select',
						check: 'isNotEmpty'
					},
					config: {
						label: asm.lang.pluginTests.pluginConfiguration,
						type: 'text'
					},
					input: {
						label: asm.lang.pluginTests.inputFile,
						type: 'file',
						hint: asm.lang.pluginTests.inputFileHint,
						check: 'hasExtension',
						checkParams: { extensions: ['zip'] }
					}
				}
			}},
			request: 'AddPluginTest',
			stores: [asm.ui.globals.stores.plugins]
		};
		if (config && config.stores) {
			$.merge(defaults.stores, config.stores);
			delete config.stores;
		}
		this.base($.extend(true, defaults, config));
	},
	_initContent: function () {
		var plugins = asm.ui.globals.stores.plugins.get(),
			pluginOpts = asm.ui.Utils.tableToOptions(plugins, 'id', 'name'),
			pluginArgs = asm.ui.Utils.tableToOptions(plugins, 'id', 'arguments'),
			pluginSelectEl = this.form('getFieldByName', 'plugin'),
			pluginConfigEl = this.form('getFieldByName', 'config');

		this.setFieldOptions('plugin', pluginOpts);

		pluginSelectEl.unbind('change.formInit').bind('change.formInit', $.proxy(function () {
			var pluginId = pluginSelectEl.field('option', 'value'),
				args = pluginArgs[pluginId],
				paramString = (args != undefined) ? args.split(';').join(', ') : '',
				hint = paramString ? (asm.lang.pluginTests.pluginConfigurationHint + paramString)
					 : asm.lang.pluginTests.pluginHasNoArguments;
			pluginConfigEl.field('option', 'hint', hint)
				.field('option', 'editable', paramString);
		}, this)).change();
	}
});