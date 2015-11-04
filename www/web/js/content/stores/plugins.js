asm.ui.globals.stores.plugins = new asm.ui.TableStore({
	cols: [
		'id',
		'name',
		'type',
		'description',
		'arguments'
	],
	request: 'GetPlugins'
});