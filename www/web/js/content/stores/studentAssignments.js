asm.ui.globals.stores.studentAssignments = new asm.ui.TableStore({
	cols: [
		'id',
		'name',
		'description',
		'pluginDescription',
		'deadline',
		'reward',
		'lecture',
		'lectureDescription',
		'group',
		'groupDescription',
		'submissionExists',
        'submissionGraded'
	],
	request: 'GetStudentAssignments'
});