/**
 * Table of plugins.
 */
asm.ui.table.Plugins = asm.ui.DynamicTable.extend({
	constructor: function (config) {
		var defaults = {
			icon: asm.ui.globals.icons.plugin,
			structure: {
				id: { key: true, hidden: true, comparable: true },
				name: { label: asm.lang.plugins.name, comparable: true, string: true },
				type: { label: asm.lang.plugins.type, comparable: true },
				description: { label: asm.lang.plugins.description, string: true },
				arguments: { label: asm.lang.plugins.arguments, string: true,	renderer: function (data) {
					return data.split(';').join(',\n');
				}}
			},
			title: asm.lang.plugins.caption,
			stores: [asm.ui.globals.stores.plugins]
		};
		this.base($.extend(true, defaults, config));
	}
});