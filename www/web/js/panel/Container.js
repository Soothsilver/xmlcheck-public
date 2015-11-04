/**
 * Base for all panels that have no "own" content but contain children panels.
 */
asm.ui.Container = asm.ui.Panel.extend({
	/**
	 * @copydoc Panel::Panel()
	 * 
	 * Additional configuration options:
	 * @arg @a children (object) children names as keys, Panel descendants as values
	 */
	constructor: function (config) {
		var defaults = {
			children: {}
		};
		this.base($.extend(defaults, config));

		this._callOnChildren('setParent', this);
		this._moveChildren(this.config.target);
	},

	/**
	 * Calls selected children method on all children.
	 * @tparam string fnName method name
	 * @tparam mixed [...] method arguments
	 */
	_callOnChildren: function (fnName) {
		var args = Array.prototype.slice.call(arguments, 1);
		for (var i in this.config.children) {
			this.config.children[i][fnName].apply(this.config.children[i], args);
		}
	},
	/**
	 * @copybrief Panel::_showContent()
	 *
	 * Shows all children (passes own display parameters to them).
	 */
	_showContent: function () {
		this._callOnChildren('show', this._params);
	},
	/**
	 * @copybrief Panel::_adjustContent()
	 *
	 * Adjusts all children (passes own display parameters to them).
	 */
	_adjustContent: function () {
		this._callOnChildren('adjust', this._params);
	},
	/**
	 * @copybrief Panel::_hideContent()
	 *
	 * Hides all children.
	 */
	_hideContent: function () {
		this._callOnChildren('hide');
	},
	/**
	 * Moves all children to new container.
	 */
	_moveChildren: function () {
		for (var i in this.config.children) {
			var newTarget = $('<div></div>')
				.addClass('ui-container')
                .removeData()
                .removeAttr('data')
                .attr('data-child-name', i)
				.appendTo(this.config.target);
			this.config.children[i].move(newTarget);
		}
	},
    _moveSingleChild: function(childId) {
        var newTarget = $('<div></div>')
            .addClass('ui-container')
            .removeData()
            .removeAttr('data')
            .attr('data-child-name', childId)
            .appendTo(this.config.target);
        this.config.children[childId].move(newTarget);
    },
	/**
	 * @copybrief Panel::_moveContent()
	 * 
	 * Calls _moveChildren().
	 */
	_moveContent: function (oldTarget) {
		this._moveChildren();
	},
	destroy: function () {
		this._callOnChildren('destroy');
		return this;
	}
});
