asm.ui.globals.stores.ratingsStudent = new asm.ui.TableStore({
	cols: [
		'user',
		'email',
		'groupId',
		'rating',
		'group',
		'groupDescription',
		'lecture',
		'lectureDescription'
	],
	request: 'GetRatingSums'
});