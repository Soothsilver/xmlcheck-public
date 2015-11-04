asm.ui.globals.stores.questions = new asm.ui.TableStore({
	cols: [
		'id',
		'text',
		'type',
		'options',
		'attachments',
		'lectureId',
		'lecture',
		'lectureDescription'
	],
	request: 'GetQuestions'
});