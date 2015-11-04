asm.ui.globals.stores.assignments = new asm.ui.TableStore({
	cols: [
		'id',
		'problemId',
		'problem',
		'deadline',
		'reward',
		'groupId',
		'group',
        'groupOwner'
	],
	request: 'GetAssignments'
});