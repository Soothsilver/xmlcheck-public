asm.ui.globals.stores.correctionRated = new asm.ui.TableStore({
	arguments: {
		rated: true
	},
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