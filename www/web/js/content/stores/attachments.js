asm.ui.globals.stores.attachments = new asm.ui.TableStore({
	cols: [
		'id',
		'name',
		'type',
		'lectureId',
		'lecture',
		'lectureDescription'
	],
	request: 'GetAttachments'
});