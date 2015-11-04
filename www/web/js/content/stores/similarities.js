asm.ui.globals.stores.similarities = new asm.ui.TableStore({
	arguments: {
		newId : 0
	},
	cols: [
		'id',
		'submissionId',
		'suspicious',
		'similarityScore',
		'similarityReport',
		'author',
		'date',
		'status'
	],
	request: 'GetSimilarities'
});