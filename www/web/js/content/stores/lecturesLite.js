asm.ui.globals.stores.lecturesLite = new asm.ui.TableStore({
	arguments: {
		'lite': true
	},
	cols: [
		'id',
		'name'
	],
	request: 'GetLectures'
});