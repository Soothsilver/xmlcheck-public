/**
 * Container with two tables: current and available subscriptions.
 */
asm.ui.panel.Subscriptions = asm.ui.Container.extend({
	constructor: function (config) {
		var defaults = {
			children: {
				current: new asm.ui.table.Subscriptions({
					actions: {
						extra: [{
							callback: $.proxy(function () {
								this.config.children.available.refresh();
							}, this),
							confirmText: asm.lang.subscriptions.confirmSubscriptionCancellationText,
							confirmTitle: asm.lang.subscriptions.confirmSubscriptionCancellation,
							expire: [asm.ui.globals.stores.subscriptions, asm.ui.globals.stores.availableGroups,
									asm.ui.globals.stores.studentAssignments],
							filter: function (id, values) {
								return (values['status'] == 'subscribed');
							},
							icon: 'ui-icon-' + asm.ui.globals.icons.cancel,
							label: asm.lang.subscriptions.cancelSubscription,
							request: 'DeleteSubscription',
							refresh: true
						}]
					}
				}),
				available: new asm.ui.table.AvailableGroups({
					actions: {
						extra: [{
							callback: $.proxy(function () {
								this.config.children.current.refresh();
							}, this),
							expire: [asm.ui.globals.stores.subscriptions, asm.ui.globals.stores.subscriptionRequests,
									asm.ui.globals.stores.availableGroups, asm.ui.globals.stores.studentAssignments],
							icon: 'ui-icon-' + asm.ui.globals.icons.add,
							label: asm.lang.subscriptions.addRequestSubscription,
							request: 'AddSubscription',
							refresh: true
						}]
					}
				})
			}
		};
		this.base($.extend(defaults, config));
	}
});