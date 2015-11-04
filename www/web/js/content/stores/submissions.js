asm.ui.globals.stores.submissions = new asm.ui.TableStore({
	cols: [
		'id',
		'problem',
		'deadline',
		'date',
		'status',
		'success',
		'details',
		'rating',
        'note',
		'hasOutput'
	],
	request: 'GetSubmissions'
});