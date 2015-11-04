asm.ui.globals.stores.groups = new asm.ui.TableStore({
	cols: [
		'id',
		'name',
		'description',
		'type',
		'lectureId',
		'lecture',
		'lectureDescription'
	],
	request: 'GetGroups'
});