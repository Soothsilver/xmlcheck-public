/**
 * Table of questions.
 */
asm.ui.table.Questions = asm.ui.DynamicTable.extend({
	constructor: function (config) {
		var defaults = {
			icon: asm.ui.globals.icons.question,
			actions : {
				raw: [
					{
						icon: 'ui-icon-' + asm.ui.globals.icons.xtest,
						label: asm.lang.questions.check,
						isToggleButton: true,
						action: $.proxy(function(id)
						{
							if (asm.ui.ArrayUtils.inArray(id, asm.ui.globals.selectedIds))
							{
								asm.ui.ArrayUtils.remove(id,  asm.ui.globals.selectedIds);
							}
							else {
								asm.ui.globals.selectedIds.push(id);
							}
						}, this)
					}
				]
			},
			structure: {
				id: { key: true, hidden: true, comparable: true },
				text: { label: asm.lang.questions.text, string: true, comparable: true },
				type: { label: asm.lang.questions.type, string: true, comparable: true, renderer: function (val) {
					switch (val) {
						case 'text':
							return asm.lang.questions.textAnswer;
						case 'choice':
							return asm.lang.questions.singleChoice;
						case 'multi':
							return asm.lang.questions.multipleChoice;
						default:
							return val;
					}
				} },
				options: { label: asm.lang.questions.options, string: true },
				lectureId: { hidden: true, comparable: true },
				lecture: { label: asm.lang.questions.lecture, comparable: true, string: true }
			},
			title: asm.lang.questions.caption,
			stores: [asm.ui.globals.stores.questions]
		};
		this.base($.extend(true, defaults, config));
	},
	_adjustContent: function () {
		var lectureId = this._params[0] || false;
		if (this._filterId != undefined) {
			this.table('removeFilter', this._filterId);
		}
		if (lectureId) {
			this._filterId = this.table('addFilter', 'lectureId', 'equal', lectureId);
		}
	}
});