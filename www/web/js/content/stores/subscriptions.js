asm.ui.globals.stores.subscriptions = new asm.ui.TableStore({
	cols: [
		'id',
		'group',
		'description',
		'lecture',
		'lectureDescription',
		'status'
	],
	request: 'GetSubscriptions'
});