asm.ui.globals.stores.problemsLite = new asm.ui.TableStore({
	arguments: {
		lite: true
	},
	cols: [
		'id',
		'name',
		'lectureId'
	],
	request: 'GetProblems'
});