asm.ui.globals.stores.correction = new asm.ui.TableStore({
	cols: [
		'id',
		'problem',
		'group',
		'date',
		'fulfillment',
		'details',
		'rating',
        'explanation',
		'reward',
        'deadline',
		'authorId',
		'author',
        'hasOutput'
	],
	request: 'GetTeacherSubmissions'
});