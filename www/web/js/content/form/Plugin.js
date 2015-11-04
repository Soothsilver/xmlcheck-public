/**
 * Add plugin form.
 */
asm.ui.form.Plugin = asm.ui.DynamicForm.extend({
	constructor: function (config) {
		var defaults = {
			formStructure: { main: {
				icon: asm.ui.globals.icons.plugin,
				caption: asm.lang.plugins.addPluginCaption,
				fields: {
					name: {
						label: asm.lang.plugins.name,
						type: 'text',
						hint: asm.lang.plugins.nameHint,
						check: ['isName', 'isNotEmpty', asm.ui.Macros.nameCheck('plugins')]
					},
					plugin: {
						label: asm.lang.plugins.file,
						type: 'file',
						hint: asm.lang.plugins.fileHint,
						check: ['hasExtension'],
						checkParams: { extensions: ['zip'] }
					}
				}
			}},
			request: 'AddPlugin'
		};
		this.base($.extend(true, defaults, config));
	}
});