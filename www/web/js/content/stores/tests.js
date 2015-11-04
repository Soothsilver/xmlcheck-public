asm.ui.globals.stores.tests = new asm.ui.TableStore({
	cols: [
		'id',
		'description',
		'template',
		'count',
		'generated',
		'lectureId',
		'lecture',
		'lectureDescription'
	],
	request: 'GetTests'
});