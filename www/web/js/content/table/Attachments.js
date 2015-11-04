/**
 * Table of attachments.
 */
asm.ui.table.Attachments = asm.ui.DynamicTable.extend({
	constructor: function (config) {
		var defaults = {
			icon: asm.ui.globals.icons.attachment,
			structure: {
				id: { key: true, hidden: true, comparable: true },
				name: { label: asm.lang.attachments.name, string: true, comparable: true },
				type: {
                    label: asm.lang.attachments.type,
                    string: true,
                    comparable: true,
                    renderer: function(kind) {
                        switch(kind)
                        {
                            case "image": return asm.lang.attachments.image;
                            case "code": return asm.lang.attachments.code;
                            case "text": return asm.lang.attachments.text;
                            default: return kind;
                        }
                    }
                },
				lectureId: { hidden: true, comparable: true },
				lecture: { label: asm.lang.attachments.lecture, comparable: true, string: true }
			},
			title: asm.lang.attachments.caption,
			stores: [asm.ui.globals.stores.attachments]
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