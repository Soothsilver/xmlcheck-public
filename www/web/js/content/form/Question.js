/**
 * Add/edit a test question form.
 */
asm.ui.form.Question = asm.ui.DynamicForm.extend({
	constructor: function (config) {
		var defaults = {
			formStructure: { main: {
				icon: asm.ui.globals.icons.question,
				caption: asm.lang.questions.editCaption,
				fields: {
					id: {
						type: 'hidden'
					},
					lecture: {
						label: asm.lang.questions.lecture,
						type: 'select',
						hint: asm.lang.questions.questionBound,
						check: 'isNotEmpty'
					},
					text: {
						label: asm.lang.questions.text,
						type: 'textarea',
						check: 'isNotEmpty'
					},
					type: {
						label: asm.lang.questions.type,
						type: 'select',
						options: {
							'text': asm.lang.questions.textAnswer,
							'choice': asm.lang.questions.singleChoice,
							'multi': asm.lang.questions.multipleChoice
						},
						check: 'isNotEmpty'
					},
					options: {
						label: asm.lang.questions.options,
						type: 'textarea',
						check: 'isNotEmpty'
					},
					attachments: {
						label: asm.lang.questions.attachments,
						type: 'multiselect'
					}
				}
			}},
			request: 'EditQuestion',
			stores: [asm.ui.globals.stores.lectures, asm.ui.globals.stores.attachments]
		};
		if (config && config.stores) {
			$.merge(defaults.stores, config.stores);
			delete config.stores;
		}
		this.base($.extend(true, defaults, config));
	},
	_initContent: function () {
		this.setFieldOptions('lecture',
				asm.ui.Utils.tableToOptions(asm.ui.globals.stores.lectures.get(), 'id', 'name'));



		var a = this.form('getFieldByName', 'id').field('option', 'value');

		var lectureEl = this.form('getFieldByName', 'lecture'),
			attachmentsEl = this.form('getFieldByName', 'attachments'),
			attachments = asm.ui.globals.stores.attachments.get();
		lectureEl.unbind('change.pageInit').bind('change.pageInit', function () {
			var lectureId = lectureEl.field('option', 'value'),
				attFiltered = $.grep(attachments, function (attachment) {
					return true;
					// This is not working: return (attachment.lectureId == lectureId);
					// Instead, we will return all attachments and possibly deny them server-side.
				}),
				noAttachments = !attFiltered.length;
			attachmentsEl.field('option', 'type', noAttachments ? 'info' : 'multiselect')
				.field('option', 'options', asm.ui.Utils.tableToOptions(
					attFiltered, 'id', 'name'));
			if (noAttachments) {
				attachmentsEl.field('option', 'value', 'N/A');
			}

		}).change();

		var typeEl = this.form('getFieldByName', 'type'),
			optionsEl = this.form('getFieldByName', 'options');
		typeEl.unbind('change.pageInit').bind('change.pageInit', function () {
			var type = typeEl.field('option', 'value'),
				enableOptions = (type != 'text'),
				hint = asm.lang.questions.firstCharacterWillBeUsedAsOptionSeparator;
			optionsEl.field('option', 'type', enableOptions ? 'textarea' : 'empty')
				.field('option', 'hint', enableOptions ? hint : '')
				.field('option', 'editable', enableOptions);
		}).change();
	}
});