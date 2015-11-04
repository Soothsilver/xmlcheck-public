/**
 * Editor of attachments.
 */
asm.ui.editor.Attachments = asm.ui.DynamicTableEditor.extend({
	constructor: function (config) {
		var defaults = {
			adjuster: function () {
				this.field('lecture', 'option', 'editable', false);
				this.field('name', 'option', 'editable', false);
				this.form('getFieldByName', 'type').change();
			},
			formClass: asm.ui.form.Attachment,
			mainStore: asm.ui.globals.stores.attachments,
			removalRequest: 'DeleteAttachment',
			subject: asm.lang.subjects.attachment,
			tableClass: asm.ui.table.Attachments
		};
		this.base($.extend(defaults, config));
	},
	_showContent: function () {
		var privileges = asm.ui.globals.session.getProperty('privileges') || {},
			privManageAttachments = privileges.lecturesManageOwn || privileges.lecturesManageAll;
		$.extend(this.config.actions, {
			add: privManageAttachments,
			manage: privManageAttachments,
			remove: privManageAttachments
		});
		this.base();
	}
});