asm.ui.globals.stores.problems = new asm.ui.TableStore({
	cols: [
		'id',
		'name',
		'description',
		'pluginId',
		'pluginArguments',
		'lectureId',
		'lecture',
		'lectureDescription'
	],
	request: 'GetProblems'
});