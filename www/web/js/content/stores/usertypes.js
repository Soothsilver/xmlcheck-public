asm.ui.globals.stores.usertypes = new asm.ui.TableStore({
	cols: [
		'id',
		'name',
		'privileges'
	],
	request: 'GetUsertypes'
});