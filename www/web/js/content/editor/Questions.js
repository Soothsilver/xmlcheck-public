/**
 * Editor of questions.
 */
asm.ui.editor.Questions = asm.ui.DynamicTableEditor.extend({
	constructor: function (config) {
		var defaults = {
			adjuster: function () {
				this.field('lecture', 'option', 'editable', false);
				this.form('getFieldByName', 'type').change();
			},
			formClass: asm.ui.form.Question,
			mainStore: asm.ui.globals.stores.questions,
			removalRequest: 'DeleteQuestion',
			subject: asm.lang.subjects.question,
			tableClass: asm.ui.table.Questions
		};
		this.base($.extend(defaults, config));
	},
	_showContent: function () {
		var privileges = asm.ui.globals.session.getProperty('privileges') || {},
			privManageQuestions = privileges.lecturesManageOwn || privileges.lecturesManageAll;
		$.extend(this.config.actions, {
			add: privManageQuestions,
			manage: privManageQuestions,
			remove: privManageQuestions
		});
		this.base();
	}
});