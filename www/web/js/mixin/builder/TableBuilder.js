/**
 * @copybrief Builder
 * 
 * Adds methods for building of tables and table parts.
 */
asm.ui.TableBuilder = asm.ui.Builder.extend({
	/**
	 * Creates table row with fields with supplied content.
	 * @tparam object fields array with contents of individual fields
	 * @treturn jQueryEl created table row
	 */
	_buildTableRow: function (fields) {
		var row = this._buildTag('tr');
		$.each(fields, function (i, content) {
			$('<td></td>').append(content)
				.appendTo(row);
		});
		return row;
	},
	/**
	 * Creates table body with supplied content.
	 * @tparam object rows multi-dimensional array with rows with field contents
	 * @treturn jQueryEl created table body
	 */
	_buildTableBody: function (rows) {
		var body = this._buildTag('tbody');
		rows = rows || [];

		for (var i in rows) {
			this._buildTableRow(rows[i])
				.appendTo(body);
		}

		return body;
	},
	/**
	 * Creates table head with supplied content.
	 * @tparam object rows multi-dimensional array with rows with field contents
	 *		(field contents can either be simple strings or arrays with label string,
	 *		colspan, and rowspan values respectively)
	 * @treturn jQueryEl created table head
	 */
	_buildTableHead: function (rows) {
		var head = this._buildTag('thead');
		rows = $.isArray(rows) ? rows : (rows ? [rows] : []);
			
		for (var i in rows) {
			var row = $('<tr></tr>').appendTo(head),
				simpleRow = $.isArray(rows[i]);
			for (var id in rows[i]) {
				var data = rows[i][id],
					header = $('<th></th>')
						.appendTo(row);
				if (simpleRow) {
					header.append(data);
				} else {
					data = (typeof data != 'string') ? data : {
						label: data,
						colspan: 1,
						rowspan: 1
					};
					header.append(data.label)
						.attr('id', id)
						.attr('colspan', data.colspan)
						.attr('rowspan', data.rowspan);
				}
			}
		}

		return head;
	},
	/**
	 * Creates table with supplied header & contents.
	 * @tparam object headers object accepted by _buildTableHead()
	 * @tparam object content object accepted by _buildTableBody()
	 * @tparam string name @optional table label
	 * @tparam string icon @optional icon name label icon
	 * @treturn jQueryEl created table element
	 */
	_buildTable: function (headers, content, name, icon) {
		var table = this._buildTag('table');
		if (name || icon) {
			var caption = $('<caption></caption>')
				.append(name)
				.appendTo(table);
			if (icon) {
				$('<div></div>')
					.addClass('ui-icon-label')
					.icon({type: icon})
					.prependTo(caption);
			}
		}
		this._buildTableHead(headers)
			.appendTo(table);
		this._buildTableBody(content)
			.appendTo(table);
		return table;
	},
	/**
	 * Applies base style to table (based on table widget style).
	 * @tparam jQueryEl table element
	 */
	_styleTable: function (table) {
		table.addClass('ui-widget-content')
			.wrap(this._buildTag('div')
				.addClass('ui-table')
				.addClass('ui-table-with-caption')
				.addClass('ui-widget'));

		var captionWrap = this._buildTag('div')
			.addClass('ui-table-caption')
			.addClass('ui-corner-top')
			.addClass('ui-state-default');
		$('caption', table).wrapInner(captionWrap);
	}
});