/**
 * Table of pending subscription requests.
 */
asm.ui.table.SubscriptionRequests = asm.ui.DynamicTable.extend({
	constructor: function (config) {
		var defaults = {
			icon: asm.ui.globals.icons.subscription,
			structure: {
				id: { key: true, hidden: true, comparable: true },
				user: { hidden: true, comparable: true, string: true },
				realName: { label: asm.lang.subscriptions.realName, comparable: true, string: true },
				email: { label: asm.lang.subscriptions.email, comparable: true, string: true },
				group: { label: asm.lang.subscriptions.group, comparable: true, string: true },
				lecture: { label: asm.lang.subscriptions.lecture, comparable: true, string: true }
			},
			title: asm.lang.subscriptions.subscriptionRequests,
			stores: [asm.ui.globals.stores.subscriptionRequests]
		};
		this.base($.extend(defaults, config));
	}
});