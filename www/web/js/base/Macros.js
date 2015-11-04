/**
 * Utility functions for shorter syntax of some commonly used actions.
 */
asm.ui.Macros = Base.extend({
	constructor: null
}, {
	/**
	 * Creates form field checking function to be used with @ref widget.field "field widget"
	 * for checking whether name entered for subject isn't already taken.
	 * @tparam string table name of subject set
	 */
	nameCheck: function (table) {
		var table = table;
		return function (value, field) {
			var field = field,
				name = value,
				error = false;
			asm.ui.globals.coreCommunicator.request('IsNameTaken', { table: table, name: name }, function (data) {
				error = data.nameTaken ? asm.lang.checks.nameTakenBefore + name + asm.lang.checks.nameTakenAfter : false;
				field.field('setError', error);
			});
			return error;
		};
	},
	/**
	 * Creates @ref widget.table "table widget" action configuration object for row
	 * removal action.
	 * @tparam object o following options:
	 * @arg @a expireOnRemoval (array) stores that should be marked as expired
	 *		on row removal success
	 * @arg @a removalMessage custom part of removal confirmation dialog text
	 * @arg @a removalRequest name of core request handling the removal
	 * @arg @a subject row/element name/description
	 */
	trashAction: function (o) {
		return {
			callback: function (id) {
				if (o.expireOnRemoval) {
					for (var i in o.expireOnRemoval) {
						o.expireOnRemoval[i].expire();
					}
				}
			},
			confirmText: asm.lang.edit.doYouReallyWantToDeleteThis_Before + o.subject +
                asm.lang.edit.doYouReallyWantToDeleteThis_After
					+ ((o.removalMessage) ? ' ' + o.removalMessage : ''),
			confirmTitle: asm.lang.edit.confirmDeletion,
			icon: 'ui-icon-' + asm.ui.globals.icons['delete'],
			label: asm.lang.edit.remove + ' ' + o.subject,
			refresh: true,
			request: o.removalRequest
		};
	}
});