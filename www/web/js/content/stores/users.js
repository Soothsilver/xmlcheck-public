asm.ui.globals.stores.users = new asm.ui.TableStore({
	cols: [
		'id',
		'username',
		'typeId',
		'type',
		'name',
		'email',
		'lastLogin'
	],
	request: 'GetUsers'
});