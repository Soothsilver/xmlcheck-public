asm.ui.panel.UserRatingTables = asm.ui.DynamicContentPanel.extend({
	constructor: function (config) {
		var defaults = {
			stores: [asm.ui.globals.stores.ratingsTeacherDetailed]
		};
		this.base($.extend(defaults, config));

		this._tables = {};
	},
	_initContent: function () {
		this.config.target.empty();

		var privileges = asm.ui.globals.session.getProperty('privileges') || {};
		var tableActions = (privileges.submissionsCorrect && privileges.submissionsViewAuthors) ? {
			local: [{
				icon: 'ui-icon-' + asm.ui.globals.icons.submission,
				label: asm.lang.userRatings.showUsersSubmission,
				action: $.proxy(function (id) {
					this.trigger('ratings.showSubmissions', { userId: id });
				}, this)
			}]
		} : {};

		var data = asm.ui.globals.stores.ratingsTeacherDetailed.get();
		for (var groupId in data) {
			var group = data[groupId];

			var colProps = {
                name: {comparable: true, string: true}
			};
			var headers = {
				name: asm.lang.userRatings.student
			};
			for (var assignmentId in group['assignments']) {
				var assignment = group['assignments'][assignmentId];
				var colId = 'assignment-' + assignmentId;
				colProps[colId] = {	comparable: true };
				headers[colId] = assignment['problem'];
			}
			colProps.sum = {comparable: true};
			headers.sum = asm.lang.userRatings.sum;

			var rows = [];
			for (var userId in group['students']) {
				var student = group['students'][userId];
                var row = ['<span style="font-weight:bold;">' + student['name'] + '</span>'];
				var ratings = student['ratings'];
				for (var assignmentId in group['assignments']) {
					var rating = (ratings[assignmentId] != undefined) ?
							ratings[assignmentId]['rating'] :
							'<span class="ui-state-disabled">N/A</span>';
					row.push(rating);
				}

				row.push('<span style="font-weight:bold;">' + student['sum'] + '</span>');
				rows.push(row);
			}

			var caption = group['name'] + ' (' + group['lecture'] + ')' +
					(group['owned'] ? ' - ' + asm.lang.userRatings.ownedByYou : '');
			var table = this._buildTable(headers, rows, caption, asm.ui.globals.icons.group)
				.appendTo(this.config.target);

			table.table({
				actions: group['owned'] ? tableActions : {},
				colProps: colProps
			});

			this._tables[groupId] = table;
		}
	},
	_adjustContent: function () {
		var showAll = (this._params[0] == undefined);
		var groupId = this._params[0] || false;
		for (var id in this._tables) {
			this._tables[id].table('option', 'collapsed', !showAll && (id != groupId));
		}
	}
});
asm.ui.panel.UserRatingTables.implement(asm.ui.TableBuilder);