asm.ui.globals.stores.correctionAbsolutelyAll = new asm.ui.TableStore({
	arguments: {
		all: true,
		absolutelyAll: true
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
        'assignmentId',
		'authorEmail',
		'status'
	],
	request: 'GetTeacherSubmissions'
});