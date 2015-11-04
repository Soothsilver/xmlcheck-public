asm.ui.globals.stores.availableGroups = new asm.ui.TableStore({
	cols: [
		'id',
		'name',
		'description',
		'type',
		'lectureId',
		'lecture',
		'lectureDescription'
	],
	request: 'GetAvailableGroups'
});