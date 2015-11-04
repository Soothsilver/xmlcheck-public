/**
 * Table of available subscriptions (not currently subscribed).
 */
asm.ui.table.AvailableGroups = asm.ui.DynamicTable.extend({
	constructor: function (config) {
		var defaults = {
			icon: asm.ui.globals.icons.group,
			structure: {
				id: { key: true, hidden: true, comparable: true },
				name: { label: asm.lang.subscriptions.group, comparable: true, string: true },
				description: { label: asm.lang.subscriptions.description, string: true },
				type: { label: asm.lang.subscriptions.type, comparable: true },
				lecture: { label: asm.lang.subscriptions.lecture, comparable: true, string: true }
			},
			title: asm.lang.subscriptions.availableSubscriptions,
			stores: [asm.ui.globals.stores.availableGroups]
		};
		this.base($.extend(true, defaults, config));
	}
});