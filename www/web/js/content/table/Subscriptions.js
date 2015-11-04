/**
 * Table of current subscriptions.
 */
asm.ui.table.Subscriptions = asm.ui.DynamicTable.extend({
	constructor: function (config) {
		var defaults = {
			icon: asm.ui.globals.icons.subscription,
			structure: {
				id: { key: true, hidden: true, comparable: true },
				group: { label: asm.lang.subscriptions.group, comparable: true, string: true },
				description: { label: asm.lang.subscriptions.description, string: true },
				lecture: { label: asm.lang.subscriptions.lecture, comparable: true, string: true },
				status: { label: asm.lang.subscriptions.status, comparable: true, string: true }
			},
			title: asm.lang.subscriptions.activeAndRequestedSubscription,
			stores: [asm.ui.globals.stores.subscriptions]
		};
		this.base($.extend(true, defaults, config));
	}
});