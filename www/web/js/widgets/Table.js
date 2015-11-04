/**
 * Enhances table element with unified 'widget' style and additional functionality.
 * Adds table collapsing, field content cutting, row sorting and filtering,
 * pagination, and custom global & row actions.
 * 
 * Row filters are added using GUI on table bottom bar, or programatically, using
 * addFilter(). Each row filter is created with two parameters - column header ID
 * and fixed value - and, when used, compares joined values of row cells in selected
 * column with fixed ID. @ref filters "Filter name" determines what type of
 * comparison used.
 */
$.widget('ui.table', {
	options: {
		/** @type object
		 * Custom global and/or row actions. Object can have two properties:
		 * @arg @a global @optional array with global actions config (such as 'add row')
		 * @arg @a local @optional array with row actions config (such as 'edit row')
		 *
		 * Each action config is an object with following properties:
		 * @arg @a action function called on action-button click with row key as
		 *		first argument and array with row field values as second argument
		 * @arg @a filter @optional Only applicable for row actions. Function called
		 *		with row key, row values array, and value getter (gets value for
		 *		column name) function as arguments. Must return boolean, true to enable
		 *		action for this row, false to disable it.
		 * @arg @a icon @optional action button icon
		 * @arg @a label @optional action button icon
		 * 
		 * where either @a icon or @a label must be set.
		 */
		actions: {
			global: [],
			local: []
		},
		/** @type bool
		 * set to true to automatically colapse table body (and lock header) when empty
		 * @note Only works with @ref collapsible set to true (default).
		 * @warning Do not turn on if you want global actions to be accessible even
		 *		when the table is empty.
		 *	@see collapsible
		 */
		autoCollapse: false,
		/** @type bool
		 * table collapsed state
		 * @note Only works with @ref collapsible set to true (default).
		 * @see collapsible
		 */
		collapsed: false,
		/** @type bool
		 * Set to true to enable table collapsing.
		 * For manual collapsing to be possible, table must have non-empty
		 * <tt>&lt;caption&gt;</tt>. Table can be (un)collapsed programatically
		 * even without caption by setting @ref collapsed property.
		 * @ref autoCollapse "Auto-collapsing" also works regardless of caption
		 * content (auto-collapsed table without caption is hidden completely).
		 */
		collapsible: true,
		/** @type object
		 * Table column properties.
		 * Object with values of column header IDs as keys and objects with flags
		 * indicating certain property as values. Following flags are supported:
		 * @arg @a comparable enable sorting and filtering for this column
		 * @arg @a hidden hide this column completely
		 * @arg @a key make this column part of table row "key"
		 * @arg @a string enable prefix, suffix, and infix filtering for this column and aligns
		 *		cell content to the left (other content is centered)
		 *
		 * Flags must be set as object properties with flag name as key and @c true
		 * as value.
		 */
		colProps: {},
		/** @type int
		 * If > 0, field contents will be cut using @ref textCutter and overflow hidden.
		 * Value will be passed to @ref textCutter as @ref textCutter::limit "limit"
		 * option.
		 */
		fieldTextLimit: 0,
		/** @type int
		 * number of rows per page (if table has more rows, it is "paginated")
		 */
		rowsPerPage: (cookies.exists('rowsPerPage') ? cookies.get('rowsPerPage') : 15),
		/** @type bool
		 * set to true to hide table bottom bar (pagination & filtering) and turn
		 * off pagination
		 */
		simple: false
	},
	_create: function () {
		this.element.addClass('ui-widget-content')
			.wrap($('<div></div>')
				.addClass('ui-widget')
				.addClass('ui-table'));
		this.wrapper = this.element.parent();
		var caption = this.element.find('caption');
		this.label = $();
		if (caption.length) {
			this.label = $('<div></div>')
				.addClass('ui-table-caption')
				.addClass('ui-state-default')
				.corner({styles: ['top']})
				.append(caption.contents())
				.appendTo(caption);
			this.wrapper.addClass('ui-table-with-caption');
		}
		this.head = this.element.find('thead');
		this.body = this.element.find('tbody');
		this._createBottom();

		this.sorters = $();
		this.headerIdColRanges = {};
		this.sortBy = null;
		this.sortAsc = true;
		this.init(true);

		this._setCollapsible();
		this._setSimple();

        if (cookies.exists('rowsPerPage'))
        {
           this.option('rowsPerPage', cookies.get('rowsPerPage'));
        }
	},
	/**
	 * Creates and initializes table bottom bar (filtering & pagination GUI).
	 */
	_createBottom: function () {
		this.bottom = $('<div></div>')
			.addClass('ui-table-bottom')
			.addClass('ui-widget-content')
			.addClass('ui-helper-clearfix')
			.corner({styles: ['bottom']});

		var createButton = function (text, icon, submit) {
			return $('<button></button>')
				.attr('title', text)
				.button({
					text: false,
					icons: {primary: 'ui-icon-' + icon},
					label: text
				});
		};

		var bottomRight = $('<div></div>')
			.addClass('ui-table-bottom-right')
			.appendTo(this.bottom);
		var filtersButtonSet = $('<div></div>')
			.addClass('ui-table-filters-buttons')
			.appendTo(bottomRight);
		var toggleFiltersCheckbox = $('<input type="checkbox"/>')
			.uniqueId('ui-table-filters-toggler-')
			.appendTo(filtersButtonSet);
		var toggleFiltersLabel = $('<label for="' + toggleFiltersCheckbox.attr('id') + '"></label>')
			.attr('title', asm.lang.table.showHideFilters)
			.appendTo(filtersButtonSet);
		var filtersContainer = $('<div></div>')
			.addClass('ui-table-filters')
			//.addClass('ui-widget-content')
			.addClass('ui-helper-clearfix')
			//.corner({styles: ['all']})
			.hide();
		var newFilterForm = $('<div></div>')
			.addClass('ui-table-filter-form')
			.hide()
			.appendTo(filtersContainer);
		var newFilterInputs = $('<div></div>')
			.addClass('ui-table-filter-form-inputs')
			.addClass('ui-widget-content')
			.corner({styles: ['left']})
			.appendTo(newFilterForm);
		var filterFieldSelect = $('<select></select>')
            .bind('focus', function() { $(this).addClass('ui-state-highlight');  } )
            .bind('blur', function() { $(this).removeClass('ui-state-highlight'); } )
			.appendTo(newFilterInputs);
		var filterNegSelect = $('<select></select>')
            .bind('focus', function() { $(this).addClass('ui-state-highlight');  } )
            .bind('blur', function() { $(this).removeClass('ui-state-highlight'); } )
			.append($('<option>').attr('value', '').append(' '))
			.append($('<option>').attr('value', 'not').append('!'))
			.appendTo(newFilterInputs);
		var filterTypeSelect = $('<select></select>')
            .bind('focus', function() { $(this).addClass('ui-state-highlight');  } )
            .bind('blur', function() { $(this).removeClass('ui-state-highlight'); } )
			.appendTo(newFilterInputs);
		var filterValueInput = $('<input type="text"/>')
            .bind('focus', function() { $(this).addClass('ui-state-highlight');  } )
            .bind('blur', function() { $(this).removeClass('ui-state-highlight'); } )
			.click(function () {
				this.select();
			})
			.appendTo(newFilterInputs);
		var newFilterButtons = $('<span></span>')
			.appendTo(newFilterForm);
		$('<button></button>').addClass('ui-no-button')
			.appendTo(newFilterButtons);
		this.filtering = {
			buttons: {
				add: createButton(asm.lang.table.addFilter, 'plus').appendTo(filtersContainer)
					.addClass('ui-table-filter-add')
					.click($.proxy(function () {
						this.filtering.buttons.add.hide();
						this.filtering.form.show();
						this.filtering.fields.column.focus().change();
					}, this)),
				confirm: createButton(asm.lang.table.confirmFilter, 'check').appendTo(newFilterButtons)
					.click($.proxy(function () {
						this.filtering.form.fadeOut($.proxy(function () {
							var f = this.filtering.fields;
							this.addFilter(f.column.val(), f.type.val(), f.value.val(),
									(f.neg.val() == 'not'));
							this.filtering.buttons.add.show();
						}, this));
					}, this)),
				cancel: createButton(asm.lang.table.cancelFilterCreation, 'close').appendTo(newFilterButtons)
					.click($.proxy(function () {
						this.filtering.form.hide();
						this.filtering.buttons.add.show();
					}, this)),
				clear: createButton(asm.lang.table.clearFilters, 'trash').appendTo(filtersButtonSet)
					.click($.proxy(function () {
						this.clearFilters();
						this.filtering.buttons.clear.button('disable');
						if (this.filtering.container.is(':visible')) {
							this.filtering.buttons.toggle.label.click();
						}
					}, this))
					.button('disable'),
				toggle: {
					checkbox: toggleFiltersCheckbox,
					label: toggleFiltersLabel.click($.proxy(function (event) {
							this.filtering.container.toggle();
							if (this.filtering.container.is(':visible')) {
								if (!this.filtering.count) {
									this.filtering.buttons.add.click();
								}
							} else {
								this.filtering.buttons.cancel.click();
							}
						}, this))
				}
			},
			container: filtersContainer,
			count: 0,
			countActive: 0,
			filters: [],
			form: newFilterForm,
			fields: {
				column: filterFieldSelect.change($.proxy(function thisFn () {
					var o = this.filtering,
						isString = !!this.options.colProps[o.fields.column.val()].string;

					if (isString === thisFn.wasString) {
						return;
					} else {
						thisFn.wasString = isString;
					}

					var types = $.extend({}, o.types),
						oldType = o.fields.type.val();
					if (!isString) {
						delete types.prefix;
						delete types.suffix;
						delete types.infix;
					}

					o.fields.type.empty();
					for (var id in types) {
						$('<option></option>').attr('value', id)
							.attr('selected', (oldType == id))
							.append(types[id])
							.appendTo(o.fields.type);
					}
				}, this)),
				neg: filterNegSelect,
				type: filterTypeSelect,
				value: filterValueInput
			},
			types: {
				equal: '==',
				greater: '>',
				lesser: '<',
				prefix: '^=',
				suffix: '$=',
				infix: '*='
			}
		};
		filtersButtonSet.buttonset();
		newFilterButtons.buttonset();
		toggleFiltersCheckbox.button({
			icons: {primary: 'ui-icon-shuffle'},
			label: '0 / 0'
		});

		var bottomLeft = $('<div></div>')
			.addClass('ui-table-bottom-left')
			.appendTo(this.bottom);
		var rowsPerPageInput = $('<input type="text"/>')
			.addClass('ui-table-rpp')
			.addClass('ui-widget-content')
			.val(this.options.rowsPerPage)
			.focus(function () {
				this.select();
			})
			.bind('change', $.proxy(function (event) {
				if (+event.currentTarget.value > 0) {
					this.option('rowsPerPage', +event.currentTarget.value);
					cookies.set('rowsPerPage', +event.currentTarget.value);
				}
			}, this));
		bottomLeft.append(asm.lang.table.footer_show + ' ')
			.append(rowsPerPageInput)
			.append(' ' + asm.lang.table.footer_rowsPerPage);

		var makeNaviButton = $.proxy(function (text, icon, page, relative) {
			return createButton(text, icon)
				.bind('click', {page: page, relative: relative}, $.proxy(function (event) {
					this.page(event.data.page, event.data.relative);
				}, this));
		}, this);
		this.navigation = {
			buttons: {
				first: makeNaviButton(asm.lang.table.firstPage, 'seek-first', 1),
				prevFast: makeNaviButton(asm.lang.table.fivePagesBackward, 'seek-prev', -5, true),
				previous: makeNaviButton(asm.lang.table.previousPage, 'triangle-1-w', -1, true),
				current: $('<span></span>').addClass('ui-table-page')
					.attr('title', asm.lang.table.currentPage),
				next: makeNaviButton(asm.lang.table.nextPage, 'triangle-1-e', 1, true),
				nextFast: makeNaviButton(asm.lang.table.fivePagesForward, 'seek-next', 5, true),
				last: makeNaviButton(asm.lang.table.lastPage, 'seek-end', -1)
			},
			container: $('<div></div>')
				.addClass('ui-table-navigation')
				.appendTo(this.bottom),
			page: 1,
			rppInput: rowsPerPageInput
		};
		$.each(this.navigation.buttons, $.proxy(function (i, button) {
			button.appendTo(this.navigation.container);
		}, this));

		filtersContainer.appendTo(this.bottom);

		this.bottom.insertAfter(this.element);
	},
	_setOption: function (key, value) {
		var oldValue = value;
		switch (key) {
			case 'rowsPerPage':
				value = (value > 0) ? value : 1;
				break;
		}
		$.Widget.prototype._setOption.call(this, key, value);
		switch (key) {
			case 'actions':
				this._setGlobalActions();
				this._setRowActions();
				this._adjustActionSpan();
				break;
			case 'autoCollapse':
			case 'collapsed':
				this._setCollapsed();
				break;
			case 'collapsible':
				this._setCollapsible();
				break;
			case 'colProps':
				this._setHidden();
				this._setSortable();
				this._setFieldClasses();
				break;
			case 'fieldTextLimit':
				this._setFieldTextLimit();
				break;
			case 'rowsPerPage':
				this._setRowsPerPage(oldValue);
				break;
			case 'simple':
				this._setSimple();
				break;
		}
	},
	_setCollapsed: function () {
		var autoCollapsed = (this.options.collapsible && this.options.autoCollapse && this.isEmpty()),
			collapsed = (this.options.collapsible && this.options.collapsed),
			toggle = (collapsed || autoCollapsed) ? 'add' : 'remove';

		this.wrapper[toggle + 'Class']('ui-table-collapsed');
		this.wrapper[(autoCollapsed ? 'add' : 'remove') + 'Class']('ui-table-autoCollapsed');

		if (this.label) {
			this.label.corner(toggle, 'bottom');
			//this.label[(autoCollapsed ? 'add' : 'remove') + 'Class']('ui-state-disabled');
		}
	},
	_setCollapsible: function () {
		var self = this,
			label = this.label,
			stateClassSwitcher = function (state, callback) {
				return function () {
					var disabled = (self.options.autoCollapse && self.isEmpty());
					label.removeClass('ui-state-default')
						.removeClass('ui-state-hover')
						.removeClass('ui-state-active')
						.addClass('ui-state-' + (disabled ? 'default' : state));
					
					if (callback && !disabled) {
						callback();
					}
				};
			};

		if (label) {
			if (this.options.collapsible) {
				this.label.bind('mouseenter.table', stateClassSwitcher('hover'))
					.bind('mouseleave.table', stateClassSwitcher('default'))
					.bind('mousedown.table', stateClassSwitcher('active'))
					.bind('mouseup.table', stateClassSwitcher('hover', $.proxy(function () {
						this.option('collapsed', !this.options.collapsed);
					}, this)))
					.attr('title', asm.lang.table.clickToShowOrHide);
			} else {
				this.label.unbind('mouseenter.table')
					.unbind('mouseleave.table')
					.unbind('mousedown.table')
					.unbind('mouseup.table')
					.removeAttr('title');
			}
		} else {
			stateClassSwitcher('default')();
			label.removeAttr('title');
		}

		this.wrapper[(this.options.collapsible ? 'add' : 'remove') + 'Class']('ui-table-collapsible');
	},
	_setRowsPerPage: function (oldValue) {
		var value = this.options.rowsPerPage;
		this.navigation.rppInput.val(value);
		this.page(Math.round((this.navigation.page - 0.5) * oldValue / value));
	},
	_setSimple: function () {
		if (this.options.simple) {
			this.wrapper.addClass('ui-table-simple');
		} else {
			this.wrapper.removeClass('ui-table-simple');
		}
	},
	_setFieldTextLimit: function () {
		this._getFields().textCutter('option', 'limit', this.options.fieldTextLimit);
	},
	/**
	 * Applies style to table head rows and populates internal column index.
	 */
	_initHead: function () {
		this.head = this.element.children('thead');
		var colRanges = this.headerIdColRanges,
			carryovers = [[]],
			rows = this._getHeaderRows();
		rows.each(function (row) {
			var col = 0,
				spaces = carryovers.shift();
			$(this).children().each(function () {
				while (spaces[col]) {
					++col;
				}
				var colspan = parseInt($(this).attr('colspan') || 1);
				var rowspan = parseInt($(this).attr('rowspan') || 1);
				if (rowspan > 1) {
					for (var r = 0; r < rowspan - 1; ++r) {
						carryovers[row] = carryovers[r] || [];
						for (var c = 0; c < colspan; ++c) {
							carryovers[r][col + c] = true;
						}
					}
				}
				var id = $(this).attr('id');
				if (id) {
					colRanges[id] = {
						col: col,
						span: colspan
					};
				}
				$(this).data('table.header.col', col)
					.data('table.header.colspan', colspan);
				col += colspan;
			});
		});
		rows.addClass('ui-state-default');
	},
	/**
	 * Initialize filter-creation component using internal column index.
	 */
	_initNewFilterForm: function () {
		var self = this;
		var colSelect = this.filtering.fields.column.empty();
		this._getHeaderRows().each(function () {
			self._getFields($(this)).each(function () {
				var id = $(this).attr('id');
				if (self.options.colProps[id].comparable) {
					$('<option></option>').attr('value', id)
						.append($(this).text().toLowerCase() || id)
						.appendTo(colSelect);
				}
			});
		});
	},
	/**
	 * Creates/modifies table cell(s) button(s).
	 * @note Must be used instead of creating widget directly, because additional
	 *		measures are needed for correct table-cell-button display.
	 *	@tparam jQuerySel elements table cells to be used as buttons
	 *	@tparam mixed optionName either object with button configuration, or name
	 *		of option to adjust (cannot be used if not all @a elements are already
	 *		buttons)
	 *	@tparam mixed value @optional option value (used only if @a optionName is
	 *		a string)
	 */
	_tableFieldButton: function (elements, optionName, value) {
		if (typeof optionName == 'string') {
			elements.button('option', optionName, value);
		} else {
			elements.button(optionName);
		}
		if (elements.not(':contains(.ui-table-button-wrapper)').length) {
			$('.ui-table-button-wrapper', elements).remove();
			elements.wrapInner($('<div></div>').addClass('ui-table-button-wrapper'));
		}
		elements.corner({styles: null});
	},
	/**
	 * Hides column header cells of columns with @c hidden @ref colProps "property".
	 */
	_setHiddenHeaders: function () {
		var fieldIndices = this._getFieldIndices(this._getColsWithProp('hidden'));

		var cls = cls,
			headers = this._getHeaders().removeClass('ui-table-field-hidden');
		headers.each(function () {
			var header = $(this),
				fromCol = header.data('table.header.col'),
				colSpan = header.data('table.header.colspan'),
				toCol = fromCol + colSpan - 1;
			for (var i in fieldIndices) {
				if ((fieldIndices[i] >= fromCol) && (fieldIndices[i] <= toCol)) {
					--colSpan;
				}
			}
			if (colSpan <= 0) {
				header.addClass('ui-table-field-hidden');
			} else {
				header.attr('colspan', colSpan);
			}
		});

	},
	/**
	 * Hides content cells in columns with @c hidden @ref colProps "property".
	 */
	_setHiddenFields: function () {
		this._getFields().removeClass('ui-table-field-hidden');
		this._getFieldsWithProp('hidden').addClass('ui-table-field-hidden');
	},
	/**
	 * Hides all cells in columns with @c hidden @ref colProps "property".
	 */
	_setHidden: function () {
		this._setHiddenHeaders();
		this._setHiddenFields();
	},
	/**
	 * Initializes column headers of columns with @c sortable @ref colProps "property"
	 * to allow table sorting by clicking on columns headers.
	 */
	_setSortable: function () {
		this.sorters.unbind('click.table')
			.removeClass('ui-table-sorter')
			.button('destroy');

		var self = this,
			headers = this.head.find('th')
				.addClass('ui-cursor-default');
		this.sorters = headers.filter(function () {
				var id = $(this).attr('id');
				return (id && self.options.colProps[id].comparable);
			})
			.removeClass('ui-cursor-default')
			.addClass('ui-table-sorter');
		this._tableFieldButton(this.sorters);

		var eventData = {'table-instance': this};
		this.sorters.bind('click.table', eventData, function (event) {
			var self = event.data['table-instance'],
				target = $(event.currentTarget),
				id = target.attr('id');
			self.sortAsc = (id != self.sortBy) || !self.sortAsc;
			self.sortBy = id;
			self._tableFieldButton(self.sorters, 'icons', {
				primary: 'ui-icon-transparent',
				secondary: 'ui-icon-transparent'
			});
			self._tableFieldButton(target, 'icons', {
				primary: 'ui-icon-triangle-1-' + (self.sortAsc ? 'n' : 's'),
				secondary: 'ui-icon-transparent'
			});
			self._sort(id);
		});
		if (this.sorters.length && !this.sortBy) {
			this.sorters.each(function () {
				var id = $(this).attr('id');
				if (!self.options.colProps[id].hidden) {
					self.sortBy = id;
					return false;	// break $.each()
				}
			});
		}
	},
	/**
	 * Gets IDs of colums with(out) selected property.
	 * @tparam string propName property name
	 * @tparam bool inverted @optional set to true to select columns without
	 *		@a propName property insead
	 *	@treturn array column IDs
	 */
	_getColsWithProp: function (propName, inverted) {
		var ret = [];
		for (var id in this.options.colProps) {
			var col = this.options.colProps[id];
			if (inverted ? !col[propName] : col[propName]) {
				ret.push(id);
			}
		}
		return ret;
	},
	/**
	 * Gets table content cells with(out) selected property.
	 * @tparam string propName property name
	 * @tparam bool inverted @optional set to true to select cells without
	 *		@a propName property insead
	 *	@treturn jQuerySel table cells
	 */
	_getFieldsWithProp: function (propName, inverted) {
		var fieldIndices = this._getFieldIndices(this._getColsWithProp(propName, inverted));

		var self = this,
			fields = $();
		this._getRows().each(function () {
			fields = fields.add(self._getFields($(this)).filter(function (index) {
				return ($.inArray(index, fieldIndices) != -1);
			}));
		});

		return fields;
	},
	/**
	 * Gets row indices of columns for supplied column header IDs.
	 * @tparam array headerIds column IDs (@b modified: invalid column
	 *		IDs will be removed)
	 * @treturn array numeric indices of columns in a row
	 */
	_getFieldIndices: function (headerIds) {
		var self = this,
			validIds = [],
			fieldIndices = [];
		$.each(headerIds, function (i, headerId) {
			if (self.headerIdColRanges[headerId]) {
				validIds.push(headerId);
				var range = self.headerIdColRanges[headerId];
				if (range.col != undefined) {
					for (var j = range.col; j < range.col + (range.span || 1); ++j) {
						if ($.inArray(j, fieldIndices) == -1) {
							fieldIndices.push(j);
						}
					}
				}
			}
		});
		headerIds.splice(0, headerIds.length);
		headerIds.push.apply(headerIds, validIds);
		return fieldIndices;
	},
	/**
	 * Applies style classes to fields with style-altering properties.
	 */
	_setFieldClasses: function () {
		this._getFields().removeClass('ui-table-field-string');
		this._getFieldsWithProp('string').addClass('ui-table-field-string');
	},
	/**
	 * Initializes table body (hides overflow content in fields, adds row actions).
	 */
	_initBody: function () {
		this.body = this.element.children('tbody');
		this._getFields().textCutter();
		this._getRows().unbind('mouseenter.table')
			.unbind('mouseleave.table')
			.unbind('click.table')
			.unbind('dblclick.table')
			.bind('mouseenter.table', $.proxy(function (event) {
				this._getFields($(event.currentTarget)).addClass('ui-state-highlight')
			}, this))
			.bind('mouseleave.table', $.proxy(function (event) {
				if (!$(event.currentTarget).hasClass('ui-selected')) {
					this._getFields($(event.currentTarget)).removeClass('ui-state-highlight');
				}
			}, this))
			.bind('click.table', $.proxy(function (event) {
				$(event.currentTarget).toggleClass('ui-selected');
				this._getFields($(event.currentTarget)).addClass('ui-state-highlight');
				this._announceSelectionChange();
			}, this))
			.bind('dblclick.table', $.proxy(function (event) {
				var show = false,
					hide = false;
				this._getFields($(event.currentTarget))
					.each(function () {
						if ($(this).textCutter('isCut') && $(this).textCutter('option', 'hidden')) {
							show = true;
						} else {
							hide = true;
						}
					})
					.textCutter('toggle', show || !hide);
			}, this));
	},
	/**
	 * Gets key of supplied row.
	 * @tparam jQuerySel row
	 * @treturn string joined values of cells in columns with @c key @ref colProps "property"
	 */
	_getRowId: function (row) {
		var rowId = '';
		$.each(this._getColsWithProp('key'), $.proxy(function (i, id) {
			rowId += this._value(row, id);
		}, this));
		return rowId;
	},
	/**
	 * Create action button from supplied table cell.
	 * @tparam jQueryEl field table cell
	 * @tparam object o object with action configuration (see @ref actions)
	 * @tparam bool global @optional set to true for global actions
	 */
	_makeAction: function (field, o, global) {
		var disabled = false;
		if ($.isFunction(o.filter)) {
			var row = field.closest('tr'),
				rowId = this._getRowId(row),
				getter = $.proxy(function (colId) {
					return this.value(rowId, colId);
				}, this);
			if (!o.filter(rowId, this._values(row), getter)) {
				disabled = true;
			}
		}
		this._tableFieldButton(field, {
			text: false,
			label: o.label,
			icons: {
				primary: o.icon
			}
		});
		if (disabled) {
			this._tableFieldButton(field, 'disabled', true);
			return;
		}
		field.bind('click.table', {action: o.action, global: global},
			$.proxy(function (event) {
				var action = event.data.action,
					target = $(event.currentTarget);
				event.stopPropagation();
				if (global) {
					action.call(window);
				} else {
					var row = target.closest('tr'),
						key = this._getRowId(row),
						values = this._values(row);
					action.call(window, key, values);
				}
				setTimeout(function () {
					target.blur();
				}, 400);
			}, this));
	},
	/**
	 * Creates new table cell flagged as action button cell.
	 * @treturn jQueryEl table cell element
	 */
	_makeActionField: function () {
		return $('<td></td>').addClass('ui-table-action-field');
	},
	/**
	 * Creates/adjusts action buttons for global actions.
	 */
	_setGlobalActions: function () {
		$('.ui-table-action-field', this.head).remove();
		var self = this,
			headerRows = this._getHeaderRows(),
			firstHeaderRow = headerRows.first();
		if (this.options.actions.global.length) {
			for (var i = this.options.actions.global.length - 1; i >= 0; --i) {
				var actionHeader = self._makeActionField()
					.attr('rowspan', headerRows.length)
					.prependTo(firstHeaderRow);
				self._makeAction(actionHeader, this.options.actions.global[i], true);
			}
		}
	},
	/**
	 * Creates/adjusts action buttons for row actions.
	 */
	_setRowActions: function () {
		$('.ui-table-action-field', this.body).remove();
		var rows = this._getRows(),
			tempClass = 'ui-table-under-construction';
		if (this.options.actions.local.length) {
			for (var i = this.options.actions.local.length - 1; i >= 0; --i) {
				rows.prepend(this._makeActionField()
						.addClass(tempClass));
				var actionFields = rows.find('.' + tempClass)
					.removeClass(tempClass);
				var actionConfig = this.options.actions.local[i];

				actionFields.each($.proxy(function (index, field) {
					this._makeAction($(field), actionConfig);
				}, this));
			}
		}
	},
	/**
	 * Adjusts sizes of action buttons so that all actions together form one column.
	 */
	_adjustActionSpan: function () {
		if (this.options.actions.global.length == this.options.actions.local.length) {
			return;
		}
		var moreGlobal = (this.options.actions.global.length > this.options.actions.local.length),
			adjustContext = moreGlobal ? this.body : this.head,
			difference = (this.options.actions.global.length - this.options.actions.local.length)
				* (moreGlobal ? 1 : -1),
			useDummies = !Math.min(this.options.actions.global.length, this.options.actions.local.length),
			createDummies = !($('.ui-table-action-field', adjustContext).length);
		if (createDummies) {
			if (adjustContext == this.head) {
				var headerRows = this._getHeaderRows();
				headerRows.first().prepend(this._makeActionField()
						.attr('rowspan', headerRows.length))
			} else {
				this._getRows().prepend(this._makeActionField());
			}
		}
		$('.ui-table-action-field:last', adjustContext).attr('colspan',
				difference + (useDummies ? 0 : 1));
	},
	/**
	 * Gets table body rows.
	 * @tparam bool filtered @optional true to get only rows that aren't currently
	 *		filtered out
	 *	@treturn jQuerySel row elements
	 */
	_getRows: function (filtered) {
		var selector = filtered ? ':not(.ui-table-filtered)' : undefined;
		return this.body.children(selector);
	},
	/**
	 * Gets selected rows.
	 * @treturn jQuerySel row elements
	 */
	_getSelectedRows: function () {
		return this.body.children('.ui-selected');
	},
	/**
	 * Gets table body content cells (as opposed to action cells).
	 * @tparam jQuerySel row @optional set to get cells only from this row
	 * @treturn jQuerySel cell elements
	 */
	_getFields: function (row) {
		if (row != undefined) {
			return row.children(':not(.ui-table-action-field):not(.ui-table-empty-action-field)');
		} else {
			return this._getRows().children(':not(.ui-table-action-field):not(.ui-table-empty-action-field)');
		}
	},
	/**
	 * Gets table head rows.
	 * @treturn jQuerySel row elements
	 */
	_getHeaderRows: function () {
		return this.head.children();
	},
	/**
	 * Gets column header cells.
	 * @tparam jQuerySel row @optional set to get cells only from this table head row
	 * @treturn jQuerySel cell elements
	 */
	_getHeaders: function (row) {
		if (row != undefined) {
			return row.children(':not(.ui-table-action-field)');
		} else {
			return this._getHeaderRows().children(':not(.ui-table-action-field)');
		}
	},
	/**
	 * Gets contents of table cells from selected row.
	 * @tparam jQueryEl row
	 * @treturn array cell contents
	 */
	_values: function (row) {
		return $.map(this._getFields(row), function (field) {
				return $(field).text();
			});
	},
	/**
	 * Gets joined contents of cells in selected row & column.
	 * @tparam jQueryEl row
	 * @tparam string key column header ID
	 */
	_value: function (row, key) {
		var range = this.headerIdColRanges[key] || {},
			fields = this._getFields($(row)),
			value = '';
		if (range.col != undefined) {
			for (var i = range.col; i < range.col + (range.span || 1); ++i) {
				value += fields.eq(i).text();
			}
		}
		return this.options.colProps[key].string ? value : parseFloat(value);
	},
	/**
	 * Sorts table rows by column.
	 * Uses internal flag to determine whether to sort rows in ascending or
	 * descending order.
	 */
	_sort: function (key) {
		var self = this,
			//key = key,
			rows = this._getRows(),
			asc = this.sortAsc;
		rows.sort(function (a, b) {
			var valA = self._value(a, key),
				valB = self._value(b, key),
				gt = asc ? 1 : -1;
                if (typeof valA == "string")
                {
                    return (valA > valB) ? -gt : ( (valA == valB) ? 0 : gt);
                }

                if (isNaN(valA))
                {
                    if (isNaN(valB))
                    {
                        return 0;
                    }
                    else
                    {
                        return gt;
                    }
                }
                else if (isNaN(valB))
                {
                    return -gt;
                }
		    	else {
                    return (valA > valB) ? -gt : ( (valA == valB) ? 0 : gt);
                }
		});
        // This reverse the order. This is correct because the sort function actually sortes the rows in the opposite order than requested by user
		$.each(rows, function (i, row) {
			$(row).prependTo(self.body);
		});

		this.page(1);
	},
	/**
	 * Creates new filter GUI and adds it to filters area on table bottom bar.
	 * Filter GUI allows user to turn filter on/off and to remove it.
	 * @tparam int filterId ID of filter to create GUI for
	 * @tparam string label filter description text
	 * @treturn jQueryEl filter GUI element
	 */
	_createFilter: function (filterId, label) {
		var filter = $('<div></div>')
			.addClass('ui-table-filter')
			.hide()
			.appendTo(this.filtering.container);
		var toggler = $('<input type="checkbox"/>').uniqueId()
			.attr('checked', true)
			.appendTo(filter)
			.bind('click', {filterId: filterId}, $.proxy(function (event) {
				this._toggleFilter(event.data.filterId, event.currentTarget.checked);
			}, this));
		$('<label></label>').attr('for', toggler.attr('id'))
			.append(label)
			.appendTo(filter);
		$('<button></button>').attr('title', 'Remove filter')
			.appendTo(filter)
			.button({
				text: false,
				icons: {primary: 'ui-icon-trash'},
				label: 'Remove filter'
			})
			.bind('click', {filterId: filterId}, $.proxy(function (event) {
				this.removeFilter(event.data.filterId);
			}, this));
		filter.show('pulsate', {times: 2}, 'normal');
		return filter.buttonset();
	},
	/**
	 * Filters table rows by currently set filters (rows that don't fit are hidden).
	 */
	_applyFilters: function () {
		var filterRow = $.proxy(function (row) {
			var passed = true;
			$.each(this.filtering.filters, $.proxy(function (i, filter) {
				if ((filter == undefined) || ($.ui.table.filters[filter.type] == undefined)
						|| filter.disabled) {
					return true;	// continue $.each()
				}
				var filterFn = $.ui.table.filters[filter.type],
					value = this._value(row, filter.headerId),
					numValue = parseFloat(value);
				value = (numValue == value) ? numValue : value;
				var matchesFilter = filterFn(value, filter.value);
				matchesFilter = filter.neg ? !matchesFilter : matchesFilter;
				if (!matchesFilter) {
					passed = false;
					return false;	// break $.each()
				}
			}, this));
			return passed;
		}, this);
		this._getRows().each(function () {
			$(this).toggleClass('ui-table-filtered', !filterRow($(this)));
		});
		this.filtering.buttons.clear.button({disabled: !this.filtering.count});
		this.filtering.buttons.toggle.checkbox.button('option', 'label',
				this.filtering.countActive + ' / ' + this.filtering.count);
		this.page();

		this._announceSelectionChange();
	},
	/**
	 * Enables/disables selected filter.
	 * @tparam int filterId filter ID
	 * @tparam bool enable true to enable filter, false to disable
	 * @treturn object filter data
	 */
	_toggleFilter: function (filterId, enable) {
		var filter = this.filtering.filters[filterId];
		if (filter != undefined) {
			filter.disabled = (enable == undefined) ? !filter.disabled : !enable;
			this.filtering.countActive += (enable ? 1 : -1);
			this._applyFilters();
		}
		return filter;
	},
	/**
	 * Triggers 'selectionChange.table' event to announce change of filtering/selection.
	 */
	_announceSelectionChange: function () {
		var filteredRows = this._getRows(true),
			selectedRows = this._getSelectedRows(),
			transformRowSet = $.proxy(function (rows) {
				var ret = [];
				rows.each($.proxy(function (i, el) {
					el = $(el);
					ret.push({
						id: this._getRowId(el),
						get: function (colId) {
							return this._value(el, colId);
						}
					});
				}, this));
				return ret;
			}, this);
		this.element.triggerHandler('selectionChange.table', {
			filtered: transformRowSet(filteredRows),
			selected: transformRowSet(selectedRows)
		});
	},
	/**
	 * Checks whether the table is currently collapsed.
	 * @treturn bool true if table is collapsed
	 * @see collapsed
	 */
	isCollapsed: function () {
		return this.wrapper.hasClass('ui-table-collapsed');
	},
	/**
	 * Checks whether the table is empty.
	 * @treturn true if table is empty
	 * @see autoCollapse
	 */
	isEmpty: function () {
		return !this._getRows().length;
	},
	/**
	 * Gets joined cell contents of cells in selected row & column.
	 * @tparam string rowId row ID (joined values of cells in @ref colProps "key"
	 *		columns)
	 *	@tparam string colId column header ID
	 *	@treturn string
	 */
	value: function (rowId, colId) {
		var self = this,
			rowId = rowId,
			colId = colId,
			value = null;
		this._getRows().each(function () {
			if (self._getRowId($(this)) == rowId) {
				value = self._value($(this), colId);
				return false; // break $.each()
			}
		});
		return value;
	},
	/**
	 * Sorts table rows by selected column.
	 * @tparam string key @optional column header ID (defaults to first sortable column)
	 * @tparam bool asc @optional true for ascending order, false for descending
	 *		(defaults to ascending if table is currently sorted by different column,
	 *		descending otherwise)
	 */
	sort: function (key, asc) {
		if (key == undefined) {
			key = this.sortBy;
		}
		var sorter = $('#' + key, this.head);
		if (sorter.length) {
			this.sortAsc = !((asc != undefined) ? asc : this.sortAsc); // <- and this
			this.sortBy = this.sortAsc ? key : null; // <- this is weird
			sorter.click();
		}
		return this.element;
	},
	/**
	 * Displays selected table page.
	 * @tparam int page number of page to display (starting with 1)
	 * @tparam bool relative @optional true to calculate page number as current
	 *		page number + @a page instead
	 * @see rowsPerPage
	 */
	page: function (page, relative) {
		var rows = this._getRows(true);
		var numPages = Math.ceil(rows.length / this.options.rowsPerPage);
		if (page == undefined) {
			page = this.navigation.page;
		}
		if (relative) {
			page = this.navigation.page + page;
		}
		if ((page <= 0) || (page > numPages)) {
			page = numPages;
		}

		rows.removeClass('ui-table-hidden');
		var maxHideIndex = (page - 1) * this.options.rowsPerPage - 1;
		var bottomFilter = (maxHideIndex < 0) ? '' : ':gt(' + maxHideIndex + ')';
		rows.not(bottomFilter + ':lt(' + this.options.rowsPerPage + ')')
			.addClass('ui-table-hidden');

		var disable = {
			first: (page <= 1),
			prevFast: (page < 6),
			previous: (page < 2),
			next: (page > numPages - 1),
			nextFast: (page > numPages - 5),
			last: (page >= numPages)
		};
		for (var i in disable) {
			var button = this.navigation.buttons[i];
			button.blur();
			window.setTimeout(asm.ui.Utils.proxy(function (button, disabled) {
				if (button.button) {
					button.button(disabled ? 'disable' : 'enable');
				}
			}, this, button, disable[i]), 250);
		}
		this.navigation.buttons.current.text(page + ' / ' + numPages);
		this.navigation.page = page;

		this.navigation.container.toggle(numPages > 1);

		return this.element;
	},
	/**
	 * Adds row filter to table (see class description).
	 * @tparam string headerId column header ID
	 * @tparam string filterName name of filter to use (see @ref filters)
	 * @tparam mixed value filter parameter (filter works by comparing value
	 *		of selected cell(s) to this value)
	 *	@tparam boolean neg @optional true to invert the filter
	 *	@treturn int filter ID
	 */
	addFilter: function (headerId, filterName, value, neg) {
		var header = $('#' + headerId, this.head);
		if (!header.length) {
			return null;
		}

		var id = this.filtering.filters.length,
			numValue = parseFloat(value);
		value = (numValue == value) ? numValue : value;
		var label = header.text().toLowerCase() || headerId,
			sign = this.filtering.types[filterName],
			negSign = neg ? '!' : '',
			escapedValue = (numValue == value) ? numValue : '"' + value + '"';
		this.filtering.filters.push({
			disabled: false,
			element: this._createFilter(id, label + ' ' + negSign + sign + ' ' + escapedValue),
			headerId: headerId,
			neg: neg,
			type: filterName,
			value: value
		});
		++this.filtering.count;
		++this.filtering.countActive;
		this._applyFilters();

		return id;
	},
	/**
	 * Enables/disables selected filter.
	 * @tparam int filterId filter ID
	 * @tparam bool enable true to enable filter, false to disable
	 * @see addFilter()
	 */
	toggleFilter: function (filterId, enable) {
		var filter = this._toggleFilter(filterId, enable);
		if (filter != undefined) {
			$(':checkbox', filter.element).attr('checked', enable)
				.button('refresh');
		}
		return this.element;
	},
	/**
	 * Removes selected filter.
	 * @tparam int filterId filter ID
	 * @tparam bool noRefresh @optional <b>do not use for public calls</b> set to
	 *		true not to update table (call _applyFilters() later to do that)
	 * @see addFilter()
	 */
	removeFilter: function (filterId, noRefresh) {
		var filter = this.filtering.filters[filterId];
		if (filter != undefined) {
			filter.element.fadeOut(function () {
				$(this).remove();
			});
			if (!filter.disabled) {
				--this.filtering.countActive;
			}
			--this.filtering.count;
			delete this.filtering.filters[filterId];
			if (!noRefresh) {
				this._applyFilters();
			}
		}

		return this.element;
	},
	/**
	 * Removes all filters.
	 */
	clearFilters: function () {
		var filters = this.filtering.filters;
		for (var i in filters) {
			this.removeFilter(i, true);
		}
		this._applyFilters();
		return this.element;
	},
	/**
	 * Initializes table head (call after manually replacing <tt>&lt;thead&gt;</tt>
	 * content).
	 * @see init()
	 */
	initHead: function () {
		this._initHead();
		this._initNewFilterForm();
		this._setSortable();
		this._setGlobalActions();
		this._adjustActionSpan();
		this._setHiddenHeaders();
		return this.element;
	},

    createActionButtonFrom : function(jqueryTd, actionConfig, rowIndex) {
        jqueryTd.addClass('ui-table-action-field');
        var disabled = false;
        if ($.isFunction(actionConfig.filter)) {
            var row = jqueryTd.closest('tr'),
                rowId = this._getRowId(row),
                getter = $.proxy(function (colId) {
                    return this.value(rowId, colId);
                }, this);
            if (!actionConfig.filter(rowId, this._values(row), getter)) {
                disabled = true;
            }
        }
        this._tableFieldButton(jqueryTd, {
            text: false,
            //label: actionConfig.label,
            icons: {
                primary: actionConfig.icon
            }
        });
        jqueryTd.attr('title', actionConfig.label );
        if (disabled) {
            this._tableFieldButton(jqueryTd, 'disabled', true);
            return;
        }
        jqueryTd.bind('click.table', {action: actionConfig.action},
            $.proxy(function (event) {
                var action = event.data.action,
                    target = $(event.currentTarget);
                event.stopPropagation();

                var row = target.closest('tr'),
                    key = this._getRowId(row),
                    values = this._values(row);

                action.call(window, key, values);

                setTimeout(function () {
                    target.blur();
                }, 400);
            }, this));
    },
    // http://stackoverflow.com/questions/1787322/htmlspecialchars-equivalent-in-javascript
    escapeHtml: function (text)
    {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function (m) { return map[m]; });
    },
    _createActionFunction: function(action, keycell, values)
    {
        return function() {
            action.call(window, keycell,  values);
        };
    },
	/**
	 * Initializes table body (call after manually replacing <tt>&lt;tbody&gt;</tt>
	 * content).
	 * @see init()
	 */
	initBody: function (sort, data) {
        // Almost-Pure HTML process:
        var tablebody = "";
        var tst = "";

        var rows = [];
        var keyColumn = -1;
        for (var column in this.options.colProps)
        {
            if (this.options.colProps[column].key)
            {
                keyColumn = column;
                break;
            }
        }
        for (var rowIndex = 0; rowIndex < data.length; rowIndex++)
        {
            var row = document.createElement('tr');
            // Add actions
            if (this.options.actions.local)
            {
                for (var i = 0; i < this.options.actions.local.length; i++)
                {
                    var actionConfig = this.options.actions.local[i];
                    var actionCell = document.createElement('td');
                    if (actionConfig.filter)
                    {
                        if (!actionConfig.filter(data[rowIndex][keyColumn], data[rowIndex]))
                        {
                            actionCell.setAttribute('class', 'ui-table-empty-action-field');
                            row.appendChild(actionCell);
                            continue;
                        }
                    }
                    actionCell.setAttribute('class', 'ui-table-action-field ui-button ui-widget ui-state-default ui-button-icon-only');
                    actionCell.setAttribute('role', 'button');
                    actionCell.setAttribute('title', actionConfig.label);
                    var innerDiv = document.createElement('div');
                    innerDiv.setAttribute('class', 'ui-table-button-wrapper');

					if (actionConfig.hasOwnProperty('isToggleButton'))
					{
						var checkmark = document.createElement('span');
						checkmark.setAttribute('class', 'ui-button-icon-primary ui-icon ui-icon-circle-check');
						innerDiv.appendChild(checkmark);
						actionCell.addEventListener('click', function(e) {
							var row = $(e.target).closest('tr')[0];
							if (row.getAttribute('data-selected') === 'true')
							{
								row.setAttribute('data-selected', 'false');
								row.setAttribute('class', '');
							}
							else
							{
								row.setAttribute('data-selected', 'true');
								row.setAttribute('class', 'question-row-selected');
							}
						});
					}
					else
					{
						var innerSpan = document.createElement('span');
						innerSpan.setAttribute('class', 'ui-button-icon-primary ui-icon ' + actionConfig.icon);
						innerDiv.appendChild(innerSpan);
					}

                    actionCell.appendChild(innerDiv);
                    actionCell.addEventListener('mouseenter', function (e) {
                        $(e.target).closest('td').addClass('ui-state-hover');
                    });
                    actionCell.addEventListener('mouseleave', function (e) {
                        $(e.target).closest('td').removeClass('ui-state-hover');
                        $(e.target).closest('td').removeClass('ui-state-active');
                     });
                    actionCell.addEventListener('mousedown', function (e) {
                        $(e.target).closest('td').addClass('ui-state-active');
                    });
                    actionCell.addEventListener('mouseup', function (e) {
                        $(e.target).closest('td').removeClass('ui-state-active');
                        $(e.target).closest('td').removeClass('ui-state-hover');
                    });

                    actionCell.addEventListener('click', this._createActionFunction(actionConfig.action,  data[rowIndex][keyColumn], data[rowIndex] ));
                    row.appendChild(actionCell);
                }
            }

            // Add fields
            for (var col in data[rowIndex])
            {
                var cell = document.createElement('td');
                // Hide fields from hidden columns
                var tdClass = "";
                if (this.options.colProps[col].hasOwnProperty('hidden') && this.options.colProps[col].hidden)
                {
                    tdClass += "ui-table-field-hidden ";
                }
                // This aligns text in "string" columns to the left, because they will likely be multiline
                if (this.options.colProps[col].hasOwnProperty('string') && this.options.colProps[col].string)
                {
                    tdClass += "ui-table-field-string ";
                }
                // Put the field
                cell.setAttribute('class', tdClass);
                cell.innerHTML = data[rowIndex][col];
                row.appendChild(cell);
            }

            rows.push(row);
        }

        var tbody = $('tbody', this.element);
        tbody.html('');
        tbody.append(rows);
       // tbody.html(tablebody);

        // Binding and text cutter
        var allRows = $('tr', tbody);
        var allCells = $('td', tbody);

        var cutterLimit = this.options.fieldTextLimit;
        // Apply text cutter
        // But because this is expensive, only do it for cells that actually require it
        allCells.each(function () {
           if ($(this).text().length > cutterLimit)
           {
               $(this).textCutter({ limit: cutterLimit });
           }
        });

        // React to mouse clicks and moves
        allRows
            .bind('mouseenter.table', $.proxy(function (event) {
                this._getFields($(event.currentTarget)).addClass('ui-state-highlight')
            }, this))
            .bind('mouseleave.table', $.proxy(function (event) {
                if (!$(event.currentTarget).hasClass('ui-selected')) {
                    this._getFields($(event.currentTarget)).removeClass('ui-state-highlight');
                }
            }, this))
            .bind('dblclick.table', $.proxy(function (event) {
                var show = false,
                    hide = false;
                $(event.currentTarget).children(':ui-textCutter')
                    .each(function () {
                        if ($(this).textCutter('isCut') && $(this).textCutter('option', 'hidden')) {
                            show = true;
                        } else {
                            hide = true;
                        }
                    })
                    .textCutter('toggle', show || !hide);
            }, this));

        // Collapse the entire table via its parent DIV tag, if it should start collapsed
        // This takes constant time.
        this._setCollapsed();

        // This sets the colspan of the global action, or of the local actions if they are shorter
        this._adjustActionSpan();


        if (sort && (this.sortBy !== null)) {
            this.sort();
        }

        // Apply user-selected filters in the UI.
        this._applyFilters();



        // Show the first page
        this.page(1);
        return this.element;
        /*
        // New and faster process:
        // Create basic HTML
        var tablebody = "";
        var tst = "";
        for (var rowIndex = 0; rowIndex < data.length; rowIndex++)
        {
            var rowHtml = "<tr>";
            // Add actions
            // Add fields
            for (var col in data[rowIndex])
            {
                // Hide fields from hidden columns
                var tdClass = "";
                if (this.options.colProps[col].hasOwnProperty('hidden') && this.options.colProps[col].hidden)
                {
                    tdClass += "ui-table-field-hidden ";
                }
                // This aligns text in "string" columns to the left, because they will likely be multiline
                if (this.options.colProps[col].hasOwnProperty('string') && this.options.colProps[col].string)
                {
                    tdClass += "ui-table-field-string ";
                }
                // Put the field
                rowHtml += "<td class=\"" + tdClass +"\">";
                rowHtml += data[rowIndex][col];
                rowHtml += "</td>";
            }
            rowHtml += "</tr>";
            tablebody += rowHtml;
        }
        var tbody = $('tbody', this.element);
        tbody.html(tablebody);

        // Binding and text cutter
        var allRows = $('tr', tbody);
        var allFields = $('td', tbody);

        // Apply text cutter
        allFields
            .textCutter({ limit: this.options.fieldTextLimit });

        // React to mouse clicks and moves
        allRows
            .bind('mouseenter.table', $.proxy(function (event) {
                this._getFields($(event.currentTarget)).addClass('ui-state-highlight')
            }, this))
            .bind('mouseleave.table', $.proxy(function (event) {
                if (!$(event.currentTarget).hasClass('ui-selected')) {
                    this._getFields($(event.currentTarget)).removeClass('ui-state-highlight');
                }
            }, this))
            .bind('dblclick.table', $.proxy(function (event) {
                var show = false,
                    hide = false;
                this._getFields($(event.currentTarget))
                    .each(function () {
                        if ($(this).textCutter('isCut') && $(this).textCutter('option', 'hidden')) {
                            show = true;
                        } else {
                            hide = true;
                        }
                    })
                    .textCutter('toggle', show || !hide);
            }, this));

        // Collapse the entire table via its parent DIV tag, if it should start collapsed
        this._setCollapsed();

        // Add local actions:
        if (this.options.actions.local.length)
        {
            for (var i = this.options.actions.local.length - 1; i >= 0; --i) {
                var actionConfig = this.options.actions.local[i];
                for (var rowIndex = 0; rowIndex < allRows.length; rowIndex++)
                {
                     var button = allRows[rowIndex].insertCell(0);
                     var buttonJQuery = $(button);
                     this.createActionButtonFrom(buttonJQuery, actionConfig, rowIndex);
                }
            }
        }
        // Hard to correct:
        this._adjustActionSpan();

        // Apply user-selected filters in the UI.
        this._applyFilters();

        // Show the first page
        this.page(1);
        return this.element;
        */
	},
	/**
	 * Initializes both table head and body.
	 * @see initHead()
	 * @see initBody()
	 */
	init: function (sort) {
		this._initHead();
		this._initNewFilterForm();
		this._setSortable();
		this._setHiddenHeaders();
		this._setGlobalActions();
		return this.element;
	}
});

$.fn.extend($.ui.table, {
	/** @type object
	 * Comparator functions for table row filters (see class description).
	 * @li @c equal <tt>a == b</tt>
	 * @li @c not <tt>a != b</tt>
	 * @li @c greater <tt>a > b</tt>
	 * @li @c lesser <tt>a < b</tt>
	 * @li @c prefix @c a starts with @c b
	 * @li @c suffix @c a ends with @c b
	 * @li @c infix @c a contains @c b
	 *
	 * where @c a is value for selected row & column and @c b is fixed filter parameter.
	 */
	filters: {
		equal: function (a, b) {
			return (a == b);
		},
		greater: function (a, b) {
			return (a > b);
		},
		lesser: function (a, b) {
			return (a < b);
		},
		prefix: function (a, b) {
			return ('' + a).match('^' + b);
		},
		suffix: function (a, b) {
			return ('' + a).match(b + '$');
		},
		infix: function (a, b) {
			return (('' + a).indexOf(b) >= 0);
		}
	}
});