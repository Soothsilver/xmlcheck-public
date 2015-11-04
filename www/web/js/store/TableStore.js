/**
 * Manages loading and caching of table-structured remote data.
 */
asm.ui.TableStore = asm.ui.Store.extend({
	/**
	 * @copydoc Store::Store()
	 * Additional configuration properties:
	 * @arg @a cols names of table columns (received data will be indexed using
	 *		these keys)
	 *	For example if keys <tt>['name', 'email']</tt> are supplied and following
	 *	data is received from server:
	 *	@code
	 *	[
	 *		['Bill Gates', 'gates\@microsoft.com', 'loves money'],
	 *		['Steve Jobs', 'jobs\@apple.com', 'loves it even more', 'and gadgets too'],
	 *		['Linus Torvalds', 'torvalds\@linux.org', 'rules']
	 *	]
	 *	@endcode
	 *	it will be transformed and stored with following structure:
	 *	@code
	 *	[{
	 *		name: 'Bill Gates',
	 *		email: 'gates\@microsoft.com'
	 *	}, {
	 *		name: 'Steve Jobs',
	 *		email: 'jobs\@apple.com'
	 *	}, {
	 *		name: 'Linus Torvalds',
	 *		email: 'torvalds\@linux.org'
	 *	}]
	 *	@endcode
	 */
	constructor: function (config) {
		var defaults = {
			cols: []
		};
		this.base($.extend(defaults, config));
	},
	/**
	 * Indexes loaded table.
	 * @tparam mixed data
	 * @treturn array table-structured, indexed data
	 * @see TableStore()
	 */
	_translate: function (data) {
		var translated = [];
		for (var i in data) {
			var translatedRow = {};
			for (var j in data[i]) {
				if (this.config.cols[j] != undefined) {
					translatedRow[this.config.cols[j]] = data[i][j];
				} else {
					translatedRow[j] = data[i][j];
				}
			}
			translated.push(translatedRow);
		}

		return translated;
	},
	/**
	 * Gets rows from stored table in which selected column has supplied value.
	 * @tparam string colId column ID (see TableStore())
	 * @tparam mixed value
	 * @treturn array rows which satisfy <tt>row[colId] == value</tt>
	 */
	getBy: function (colId, value) {
		return $.grep(this.get() || [], function (line) {
			return (line[colId] == value);
		}) || [];
	}
});