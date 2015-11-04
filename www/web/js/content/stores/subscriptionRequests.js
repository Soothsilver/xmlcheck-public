asm.ui.globals.stores.subscriptionRequests = new asm.ui.TableStore({
	cols: [
		'id',
		'user',
		'realName',
		'email',
		'group',
		'description',
		'lecture',
		'lectureDescription'
	],
	request: 'GetSubscriptionRequests'
});