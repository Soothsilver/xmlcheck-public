/**
 * Base of table panels filled with data from server.
 */
asm.ui.DynamicTable = asm.ui.DynamicContentPanel.extend({
	/**
	 * @copydoc DynamicContentPanel::DynamicContentPanel()
	 *
	 * Additional configuration options:
	 * @arg @a actions object with @c extra and @c raw properties containing
	 *		high-level and low-level action configuration objects respectively
	 * @arg @a filter set to filter data retrieved from store with this function
	 *		(can be used to show only rows meeting certain requirements)
	 * @arg @a icon table caption icon
	 * @arg @a structure passed to TableBuilder::_buildTable() with additional
	 *		optional properties @c label and @c renderer used as column header
	 *		label and field renderer function respectively (field renderer transforms
	 *		values of cells in that column into more appealing/readable format)
	 * @arg @a title table caption text
	 * @arg @a transformer set to transform data retrieved from store with this
	 *		function (can be used to parse store data)
	 *	@arg @a widgetConfig configuration overrides for Table widget
	 */
	constructor: function (config) {
		var defaults = {
			actions: {
				extra: [],
				raw: []
			},
			filter: null,
			icon: null,
			structure: {},
			title: null,
			transformer: null
		};
		this.base($.extend(true, defaults, config));
	},
	/**
	 * @copybrief ContentPanel::_buildContent()
	 *
	 * Builds table using structure from configuration and creates
	 * @ref widget.table "table widget" from it.
	 */
	_buildContent: function () {
		var o = this.config;

		var colProps = $.extend(true, {}, o.structure);
		var headers = {};
		for (var id in colProps) {
			headers[id] = colProps[id].label || '';
			delete colProps[id].label;
			delete colProps[id].renderer;
		}

		var actions = $.merge([], o.actions.raw);
		$.merge(actions, this._makeExtraActions(o.actions.extra));
		actions = this._splitActions(actions);

		this._tableElem = this._buildTable(headers, null, o.title, o.icon)
			.appendTo(o.target)
			.table($.extend({
				actions: actions,
				autoCollapse: !actions.global.length,
				colProps: colProps,
				fieldTextLimit: 100
			}, o.widgetConfig))
			.bind('selectionChange.table', $.proxy(function (event, data) {
				this.trigger('table.selectionChange', data);
				return false;
			}, this));
	},
	/**
	 * Splits actions by their @c action flag to global and local action arrays.
	 * @tparam array actions action configuration objects
	 * @treturn object object with two properties:
	 *	@li @c global @a actions that have @c global property set to true
	 *	@li @c local the rest of @a actions
	 */
	_splitActions: function (actions) {
		var ret = {
			global: [],
			local: []
		};
		for (var i in actions) {
			if (actions[i].global) {
				ret.global.push(actions[i]);
			} else {
				ret.local.push(actions[i]);
			}
		}
		return ret;
	},
	/**
	 * Creates action configuration object from supplied high-level options.
	 * @tparam string icon name of action button icon
	 * @tparam string label action button text
	 * @tparam function callback called on action button click
	 * @tparam object options additional options (all optional):
	 *	@arg @a confirmText (string) set to show confirmation dialog with this text
	 *		before performing the action
	 *	@arg @a confirmTitle @optional (string) confirmation dialog title (works
	 *		only with @a confirmText set)
	 *	@arg @a expire (array) stores to be marked as expired on action completion
	 *	@arg @a filter (function) row filtering function
	 *	@arg @a global (bool) global action flag
	 *	@arg @a refresh (bool) true to refresh table on action completion
	 *	@arg @a request (string) name of core request handling this action (row key
	 *		is sent as request argument @c id) - if set, @a callback is called on
	 *		data returned from server in response
	 *	@treturn object action configuration object (see @ref widget.table::actions)
	 */
	_makeAction: function (icon, label, callback, options) {
		var defaults = {
			confirmText: null,
			confirmTitle: undefined,
			expire: undefined,
			refresh: false,
			request: null
		};
		var o = $.extend(defaults, options),
			actionFn = callback || $.noop;

        // We are now modifying the "action function" which is what happens when the button is clicked.
        // We start with the actual callback.

        // If "expire array" is set, then the action function, in addition to the callback, will also clear the caches of the caches in the expire array.
		if (o.expire) {
			var innerAction0 = actionFn;
			o.expire = $.isArray(o.expire) ? o.expire : [o.expire];
			actionFn = function () {
				for (var i in o.expire) {
					o.expire[i].expire();
				}
				innerAction0.apply(this, arguments);
			};
		}

        // If "refresh" is set, then the action function, in addition to the callback, and expiring caches (if expire was set), will refresh the table itself.
		if (o.refresh) {
			var innerAction1 = actionFn;
			actionFn = $.proxy(function () {
				innerAction1.apply(this, arguments);
				this.refresh(true);
			}, this);
		}

        // If "request" is set, then the callback, and refreshing and expiring, if set, will happen only as a result of a successful AJAX request to the server.
		if (o.request) {
			var innerAction2 = actionFn,
				self = this;
			actionFn = function (id) {
				var args = arguments,
					callback = function () {
						innerAction2.apply(this, args);
					};
				asm.ui.globals.coreCommunicator.request(o.request, {id: id}, callback, $.noop, function (errors) {
					self._triggerError(errors);
				});
			};
		}

        // If "confirmText" is set, then the action function, which may be an AJAX requestion initiation or an immediate action, will only be performed as a result of the confirmation dialog.
		if (o.confirmText) {
			var innerAction3 = actionFn;
			actionFn = function (id, values) {
				var args = arguments,
					callback = function () {
						innerAction3.apply(this, args);
					};
				if (!$.isFunction(o.confirmFilter) || o.confirmFilter(id, values)) {
					asm.ui.globals.dialogManager.confirm(callback, o.confirmText, o.confirmTitle);
				} else {
					callback();
				}
			};
		}

		return {
			action: actionFn,
			filter: o.filter,
			global: o.global,
			icon: icon,
			label: label
		};
	},
	/**
	 * Creates array of action configuration objects from high-level configuration
	 * objects.
	 * @tparam array actions high-level configuration objects with properties as
	 *		fourth argument of _makeAction() plus properties @c icon, @c label, and
	 *		@c callback, passed to _makeAction() as first, second, and third arguments
	 *		respectively
	 *	@treturn array low-level action objects (see @ref widget.table::actions)
	 */
	_makeExtraActions: function (actions) {
		var ret = [];
		for (var i in actions) {
			var o = actions[i];
			ret.push(this._makeAction(o.icon, o.label, o.callback, o));
		}
		return ret;
	},
	/**
	 * @copybrief DynamicContentPanel::_initContent()
	 *
	 * Fills table with data from first store in list of used stores if at least
	 * one store is used.
	 */
	_initContent: function () {
		if (this.config.stores.length) {
			this.fill(this.config.stores[0].get());
		}
	},
	/**
	 * Filters table-like data using filter function from configuration.
	 * @tparam array data
	 * @treturn array filtered part of @a data (or all @a data if filtering function
	 *		is not set)
	 */
	_filterData: function (data) {
		if (this.config.filter) {
			data = $.grep(data, this.config.filter);
		}
		return data;
	},
	/**
	 * Transforms table-like data using transformer function from configuration on
	 * every row.
	 * @tparam array data
	 * @treturn array transformed @a data (or unchanged if transformer function
	 *		is not set)
	 */
	_transformData: function (data) {
		var transformed = [];
		if (this.config.transformer) {
			for (var i in data) {
				transformed.push(this.config.transformer($.extend(true, {}, data[i])));
			}
		} else {
			transformed = $.merge([], data);
		}
		return transformed;
	},
	/**
	 * Transforms table-like data using appropriate column renderers on all fields.
	 * @tparam array data
	 * @treturn array data with transformed fields in columns that have renderer
	 *		property set
	 */
	_renderFields: function (data) {
		var rendered = [];
		for (var i in data) {
			var row = data[i],
				renderedRow = {};
			for (var id in this.config.structure) {
				var cfg = this.config.structure[id];
				renderedRow[id] = (cfg.renderer ? cfg.renderer(row[id]) : row[id]) || '';
			}
			rendered.push(renderedRow);
		}
		return rendered;
	},
	destroy: function () {
		this.table('destroy');
		this.base();
	},
	/**
	 * Calls @ref widget.table "table widget" method on table.
	 * @tparam mixed [...] arguments passed to widget method
	 * @returns mixed widget method output
	 */
	table: function () {
		return this._tableElem.table.apply(this._tableElem, arguments);
	},
	/**
	 * Fills table body with supplied data and connects it to table widget.
	 * @tparam array data
	 */
	fill: function (data) {
        var filteredData = this._filterData(data);
        var transformedData = this._transformData(filteredData);
        var renderedFields = this._renderFields(transformedData);
        this.table('initBody', true, renderedFields);
	}
});
asm.ui.DynamicTable.implement(asm.ui.TableBuilder);