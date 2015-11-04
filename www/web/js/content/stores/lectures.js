asm.ui.globals.stores.lectures = new asm.ui.TableStore({
	cols: [
		'id',
		'name',
		'description'
	],
	request: 'GetLectures'
});