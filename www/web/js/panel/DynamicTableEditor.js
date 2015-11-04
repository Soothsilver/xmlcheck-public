/**
 * Base of composite "editor" panels containing a table and a form for editing
 * elements in that table.
 * Provides means to easily select "add", "edit" and "delete" actions and
 * transparently connects form and table together.
 */
asm.ui.DynamicTableEditor = asm.ui.ContentSwitcher.extend({
	/**
	 * @copydoc ContentSwitcher::ContentSwitcher()
	 *
	 * Additional configuration options:
	 * @arg @a actions (object) (see below for supported properties)
	 * @arg @a adjuster (function) used to adjust form after filling it (called in
	 *		scope of contained DynamicForm)
	 * @arg @a filler (function) used to fill form with table row data (called in
	 *		scope of contained DynamicForm)
	 *	@arg @a mainStore (TableStore) main store used by editor to fill table & form
	 * @arg @a removalMessage (string) custom message to be displayed in removal
	 *		confirmation dialog
	 * @arg @a removalRequest (string) core request to handling element removal
	 * @arg @a subject (string) name/description of table element
	 *
	 * Supported @a actions object properties:
	 * @li @c add set to true to allow global action "add new element"
	 * @li @c manage set to true to allow row action "edit this element"
	 * @li @c remove set to true to allow row action "remove this element"
	 */
	constructor: function (config) {
		var defaults = {
			actions: {
				add: false,
				manage: false,
				remove: false
			},
			adjuster: $.noop,
			filler: $.noop,
			removalMessage: '',
			removalRequest: null,
			subject: null
		};

		var mainStore = config.mainStore;
		delete config.mainStore;

		var thisTableEditor = this;
		$.extend(defaults, {
			children: {
				form: new config.formClass({
					callbacks: {
						success: function () {
							if (thisTableEditor.config.expireOnEdit) {
								for (var i in thisTableEditor.config.expireOnEdit) {
									thisTableEditor.config.expireOnEdit[i].expire();
								}
							}
							thisTableEditor.explore();
						}
					},
					stores: [mainStore]
				}).extend({
					_initContent: function () {
						this.fields('reset');
						this.base();
						this.fields('updateDefaultState');
					},
					adjust: function (params, force) {
						if (this._shown) {
							switch (thisTableEditor._state) {
								case asm.ui.DynamicTableEditor.STATE_ADD:
									this.fields('reset');
									break;
								case asm.ui.DynamicTableEditor.STATE_EDIT:
									if (!thisTableEditor.config.filler.call(this, thisTableEditor._editId)) {
										this._triggerError(new asm.ui.Error('Cannot edit requested item (id not found).'));
										thisTableEditor.explore();
									} else {
										thisTableEditor.config.adjuster.call(this);
									}
									break;
							}
						}
						this.base(params, force);
					}
				}),
				table: new config.tableClass({
					stores: [mainStore]
				}).extend({
					_buildContent: function () {
						var a = thisTableEditor.config.actions,
							o = thisTableEditor.config,
							repo = this.config.actions.extra;

						if (o.subject) {
							if (a.add) {
								repo.push({
									callback: $.proxy(thisTableEditor.add, thisTableEditor),
									global: true,
									icon: 'ui-icon-' + asm.ui.globals.icons.create,
									label: asm.lang.edit.add + ' ' + o.subject
								});
							}
							if (a.manage) {
								repo.push({
									callback: $.proxy(thisTableEditor.edit, thisTableEditor),
									icon: 'ui-icon-' + asm.ui.globals.icons.edit,
									label: asm.lang.edit.edit + ' ' + o.subject
								});
							}
							if (a.remove) {
								repo.push(asm.ui.Macros.trashAction(o));
							}
						}

						this.base();
					}
				})
			},
			expireOnEdit: $.merge([mainStore], config.expireOnEdit || []),
			expireOnRemoval: $.merge([mainStore], config.expireOnRemoval || []),
			filler: function (id) {
				var tableData = mainStore.getBy('id', id),
					data = tableData[0] || null;

				if (!data) {
					return false;
				}

				this.fill(data);
				return true;
			}
		});
		delete config.expireOnEdit;
		delete config.expireOnRemoval;
		this.base($.extend(defaults, config));
	},
	/**
	 * @copybrief ContentSwitcher::_adjustContent()
	 *
	 * Sets internal editor state based on first display parameter:
	 * @li @c 'add' => @ref STATE_ADD (rest of parameters is passed to
	 *		@ref DynamicForm "form")
	 * @li @c 'edit' => @ref STATE_EDIT (second parameter is used as row key of
	 *		row/element to edit, rest is passed to @ref DynamicForm "form")
	 * @li other value => @ref STATE_EXPLORE (all display parameters are passed
	 *		to @ref DynamicTable "table")
	 */
	_adjustContent: function () {
		var self = asm.ui.DynamicTableEditor,
			params = this._params,
			next = 'form',
			refresh = true;

		switch (params[0] || '') {
			case 'add':
				this._state = self.STATE_ADD;
				params = params.slice(1);
				break;
			case 'edit':
				this._state = self.STATE_EDIT;
				this._editId = (this._params[1] != undefined) ? this._params[1] : null;
				params = params.slice(2);
				break;
			default:
				this._state = self.STATE_EXPLORE;
				next = 'table';
				refresh = false;
		}

		if (refresh) {
			this._callOnCurrentChild('hide');
			this.config.current = '';
		}
		this._params = $.merge([next], params);

		this.base();

		this.trigger('editor.stateChange', { state: this._state });
	},
	/**
	 * Requests adjustment of display parameters so as to show form in "add" state.
	 */
	add: function () {
		this._requestAdjust(['add']);
	},
	/**
	 * Requests adjustment of display parameters so as to show form in "edit" state.
	 * @tparam string id ID of element to be edited
	 */
	edit: function (id) {
		this._requestAdjust(['edit', id]);
	},
	/**
	 * Requests adjustment of display parameters so as to show table.
	 */
	explore: function () {
		this._requestAdjust();
	}
}, {
	/** table is shown (default state) */
	STATE_EXPLORE: 0,
	/** empty form is shown */
	STATE_ADD: 1,
	/** form filled with element data is shown */
	STATE_EDIT: 2
});