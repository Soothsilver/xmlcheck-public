/**
 * Add attachment form.
 */
asm.ui.form.Attachment = asm.ui.DynamicForm.extend({
	constructor: function (config) {
		var defaults = {
			formStructure: { main: {
				icon: asm.ui.globals.icons.attachment,
				caption: asm.lang.attachments.editCaption,
				fields: {
					id: {
						type: 'hidden'
					},
					lecture: {
						label: asm.lang.attachments.lecture,
						type: 'select',
						hint: asm.lang.attachments.attachmentBound,
						check: 'isNotEmpty'
					},
					name: {
						label: asm.lang.attachments.name,
						type: 'text',
						check: ['isName', 'isNotEmpty']
					},
					type: {
						label: asm.lang.attachments.type,
						type: 'select',
						options: {
							text: asm.lang.attachments.text,
							code: asm.lang.attachments.code,
							image: asm.lang.attachments.image
						},
						check: 'isNotEmpty'
					},
					file: {
						label: asm.lang.attachments.file,
						type: 'file',
						check: 'isNotEmpty'
					}
				}
			}},
			request: 'EditAttachment',
			stores: [asm.ui.globals.stores.lectures]
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

		var typeEl = this.form('getFieldByName', 'type'),
			fileEl = this.form('getFieldByName', 'file');
		typeEl.unbind('change.pageInit').bind('change.pageInit', function () {
			var type = typeEl.field('option', 'value'),
				restrictExtensions = (type == 'image'),
				hint = asm.lang.attachments.useImagesHint,
				extensions = ['gif', 'png', 'jpeg', 'jpg', 'bmp'];
			fileEl.field('reset')
				.field('option', 'hint', restrictExtensions ? hint : '')
				.field('option', 'check', restrictExtensions ? 'hasExtension' : 'notEmpty')
				.field('option', 'checkParams', restrictExtensions ? { extensions: extensions } : {});
		}).change();
	}
});