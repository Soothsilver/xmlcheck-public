/**
 * Utility functions not fitting in any specific category.
 * @see ArrayUtils
 * @see ObjectUtils
 * @see StringUtils
 * @see TimeUtils
 */
asm.ui.Utils = Base.extend({
	constructor: null
}, {
	/**
	 * Creates simple "options" object from multi-dimensional "table".
	 * Two values are taken from every row, one is used as key, other as value.
	 * Keys must be unique for this function to work properly. Sample use:
	 * @code
	 * asm.ui.Utils.tableToOptions([{
	 *		name: 'John Doe',
	 *		birth: '12.7.1984',
	 *		email: 'johndoe\@somedomain.com'
	 *	}, {
	 *		name: 'Margaret Brown',
	 *		birth: '3.2.1956',
	 *		email: 'm.brown\@jquery.rocks'
	 * }], 'name', 'email');
	 * @endcode
	 * yields
	 * @code
	 * {
	 *		'John Doe': 'johndoe\@somedomain.com',
	 *		'Margaret Brown': 'm.brown\@jquery.rocks'
	 * }
	 * @endcode
	 * @tparam array table array of table rows (objects with same keys)
	 * @tparam string nameId index of keys
	 * @tparam string valueId index of values
	 * @treturn object see method description
	 */
	tableToOptions: function (table, nameId, valueId) {
		var options = {};
		for (var i in table) {
			options[table[i][nameId]] = table[i][valueId];
		}
		return options;
	},
	/**
	 * Evaluates supplied data as predicate in conjunctive-normal form.
	 * Individual sub-expressions can either be simple booleans or other values.
	 * If latter, @a matcher should be supplied to turn them to booleans on
	 * expression evaluation. Sample use (checking if user is permitted to do
	 * certain action):
	 * @code
	 * asm.ui.Utils.matchCNF([	// conjunction
	 *		['canCreateUsers', 'canEditUsers', 'canDeleteUsers'],	// disjunction
	 *		'canSendEmails'	// simple expression
	 *		['canViewLog', 'canClearLog'],	// disjunction
	 *	], function (value, data) {
	 *		return data[value] || false;
	 *	}, {
	 *		canCreateUsers: true,
	 *		canDeleteUsers: true,
	 *		canSendEmails: true
	 *	});
	 * @endcode
	 * returns false, because neither 'canViewLog' nor 'canClearLog' evaluated as
	 * true.
	 * @tparam mixed cnf array to be evaluated as conjunction of disjunctions
	 * @tparam function matcher @optional evaluates individual sub-expressions
	 * @tparam mixed data passed to @a matcher on every call if @a matcher is set
	 * @treturn bool predicate value (see method description)
	 * @see matchDNF()
	 */
	matchCNF: function (cnf, matcher, data) {
		var matcher = matcher,
			data = data,
			ret = true;
		if (typeof cnf != 'object') {
			cnf = [cnf];
		}
		$.each(cnf, function (i, disj) {
			if (typeof disj != 'object') {
				disj = [disj];
			}
			$.each(disj, function (j, item) {
				ret = $.isFunction(matcher) ? matcher(item, data) : item;
				if (!ret) {
					return false;	// break $.each()
				}
			});
			if (ret) {
				return false;	// break $.each()
			}
		});
		return ret;
	},
	/**
	 * Evaluates supplied data as predicate in disjunctive-normal form.
	 * Used in similar fashion as matchCNF().
	 * @tparam mixed dnf array to be evaluated as disjunction of conjunctions
	 * @tparam function matcher @optional evaluates individual sub-expressions
	 * @tparam mixed data passed to @a matcher on every call if @a matcher is set
	 * @treturn bool predicate value (see matchCNF())
	 */
	matchDNF: function (dnf, matcher, data) {
		var matcher = matcher,
			data = data,
			ret = true;
		if (typeof dnf != 'object') {
			dnf = [dnf];
		}
		$.each(dnf, function (i, conj) {
			if (typeof conj != 'object') {
				conj = [conj];
			}
			$.each(conj, function (j, item) {
				ret = $.isFunction(matcher) ? matcher(item, data) : item;
				if (ret) {
					return false;	// break $.each()
				}
			});
			if (!ret) {
				return false;	// break $.each()
			}
		});
		return ret;
	},
	/**
	 * Gets unique ID for new HTML element to be inserted to DOM.
	 */
	getUniqueElementId: function () {
		var id = (+new Date()),
			step = 1;
		while ($('#' + id).length) {
			id += step;
			step *= 2;
		}
		return id;
	},
	/**
	 * Attaches custom scope and arguments to passed callback.
	 * @tparam function fn
	 * @tparam mixed scope
	 */
	proxy: function (fn, scope) {
		var proxyArguments = Array.prototype.slice.call(arguments, 2);
		return function () {
			fn.apply((scope || window), proxyArguments.concat(Array.prototype.slice.call(arguments)));
		};
	}
});