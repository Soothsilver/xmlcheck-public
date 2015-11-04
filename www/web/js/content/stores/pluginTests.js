asm.ui.globals.stores.pluginTests = new asm.ui.TableStore({
	cols: [
		'id',
		'description',
		'plugin',
		'pluginDescription',
		'pluginArguments',
		'arguments',
		'status',
		'fulfillment',
		'details',
		'hasOutput'
	],
	request: 'GetPluginTests'
});