/**
 * Form for generating tests from sets of test questions (question selection
 * has to happen outside the form).
 */
asm.ui.form.GenerateTest = asm.ui.DynamicForm.extend({
	constructor: function (config) {
		var defaults = {
			preSubmitAction : function(form, data)
			{
				form[3].value =  asm.ui.globals.selectedIds.join(',');
				data.questions  =asm.ui.globals.selectedIds.join(',');
			},
			formStructure: { main: {
				icon: asm.ui.globals.icons.xtest,
				caption: asm.lang.questions.generateNewTestCaption,
				fields: {
					description: {
						label: asm.lang.questions.description,
						type: 'text',
						check: ['hasLength', 'isNotEmpty']
					},
					count: {
						label: asm.lang.questions.numberOfQuestions,
						type: 'select',
						check: 'isNotEmpty'
					},
					questions: {
						type: 'hidden',
						check: 'isNotEmpty'
					},
					questionHint: {
						label: asm.lang.questions.questionsList,
						type: 'info',
						value: asm.lang.questions.selectAndFilterQuestionsAbove
					}
				}
			}},
			request: 'AddTest'
		};
		this.base($.extend(true, defaults, config));
	},

	setQuestionCount: function ( questionCount) {
		// var questionsEl = this.form('getFieldByName', 'questions'),
		var	countEl = this.form('getFieldByName', 'count'),
			countOptions = [];

		for (var j = 1; j <= questionCount; ++j) {
			countOptions.push(j);
		}
		countEl.field('option', 'options', countOptions);
	}
});