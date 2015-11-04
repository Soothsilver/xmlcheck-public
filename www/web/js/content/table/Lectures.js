/**
 * Table of lectures.
 */
asm.ui.table.Lectures = asm.ui.DynamicTable.extend({
	constructor: function (config) {
		var defaults = {
			actions: {
				raw: [{
					icon: 'ui-icon-' + asm.ui.globals.icons.problem,
					label: asm.lang.lectures.showProblems,
					action: $.proxy(function (id) {
						this.trigger('lectures.showProblems', { lectureId: id });
					}, this)
				}, {
					icon: 'ui-icon-' + asm.ui.globals.icons.question,
					label: asm.lang.lectures.showQuestions,
					action: $.proxy(function (id) {
						this.trigger('lectures.showQuestions', { lectureId: id });
					}, this)
				}, {
					icon: 'ui-icon-' + asm.ui.globals.icons.attachment,
					label: asm.lang.lectures.showAttachments,
					action: $.proxy(function (id) {
						this.trigger('lectures.showAttachments', { lectureId: id });
					}, this)
				}, {
					icon: 'ui-icon-' + asm.ui.globals.icons.xtest,
					label: asm.lang.lectures.showTests,
					action: $.proxy(function (id) {
						this.trigger('lectures.showTests', { lectureId: id });
					}, this)
				}]
			},
			icon: asm.ui.globals.icons.lecture,
			structure: {
				id: { key: true, hidden: true, comparable: true },
				name: { label: asm.lang.lectures.name, comparable: true, string: true },
				description: { label: asm.lang.lectures.description, string: true }
			},
			title: asm.lang.lectures.caption,
			stores: [asm.ui.globals.stores.lectures]
		};
		this.base($.extend(true, defaults, config));
	}
});