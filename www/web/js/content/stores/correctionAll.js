asm.ui.globals.stores.correctionAll = new asm.ui.TableStore({
	arguments: {
		all: true
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
        'hasOutput',
        'assignmentId'
	],
	request: 'GetTeacherSubmissions'
});