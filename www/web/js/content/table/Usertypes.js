/**
 * Table of user types.
 */
asm.ui.table.Usertypes = asm.ui.DynamicTable.extend({
	constructor: function (config) {
		var iconizePrivs = function (data) {
			var iconized = $('<div></div>');
			for (var i in data) {
				$('<div></div>').addClass('privilege-icon')
					.icon({
						type: data[i][1],
						title: data[i][0]
					})
					.appendTo(iconized);
			}
			return iconized.html();
		};
		var defaults = {
			icon: asm.ui.globals.icons.usertype,
			structure: {
				id: { key: true, hidden: true, comparable: true },
				name: { label: asm.lang.usertypes.name, comparable: true, string: true },
				privsUsers: { label: asm.lang.usertypes.users, renderer: iconizePrivs },
				privsSubscriptions: { label: asm.lang.usertypes.subscriptions, renderer: iconizePrivs },
				privsPlugins: { label: asm.lang.usertypes.plugins, renderer: iconizePrivs },
				privsAssignments: { label: asm.lang.usertypes.assignments, renderer: iconizePrivs },
				privsSubmissions: { label: asm.lang.usertypes.correction, renderer: iconizePrivs },
				privsLectures: { label: asm.lang.usertypes.lectures, renderer: iconizePrivs },
				privsGroups: { label: asm.lang.usertypes.groups, renderer: iconizePrivs },
				privsOther: { label: asm.lang.usertypes.other, renderer: iconizePrivs }
			},
			title: asm.lang.usertypes.caption,
			transformer: function (row) {
				var privileges = row.privileges;
				delete row.privileges;
				for (var subj in asm.ui.globals.privilegesBreakdown) {
					var actions = asm.ui.globals.privilegesBreakdown[subj],
						fieldId = 'privs' + asm.ui.StringUtils.ucfirst(subj);
					row[fieldId] = [];
					for (var action in actions) {
						if (privileges[actions[action][0]]) {
							row[fieldId].push([actions[action][2], actions[action][1]]);
						}
					}
				}
				return row;
			},
			stores: [asm.ui.globals.stores.usertypes]
		};
		this.base($.extend(true, defaults, config));
	}
});