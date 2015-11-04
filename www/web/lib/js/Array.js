if (!Array.prototype.indexOf) {
	Array.prototype.indexOf = function (el, start) {
		var start = start || 0;
		for (var i = start, j = this.length; i < j; ++i) {
			if (this[i] === el) {
				return i
			}
		}
		return -1
	}
}

if (!Array.prototype.lastIndexOf) {
	Array.prototype.lastIndexOf = function (el, start) {
		var start = start || this.length;
		if (start >= this.length) {
			start = this.length
		} else if (start < 0) {
			start = this.length + start
		}
		for (var i = start; i >= 0; --i) {
			if (this[i] === el) {
				return i
			}
		}
		return-1
	}
}

if (!Array.prototype.every) {
	Array.prototype.every = function (fn, scope) {
		var scope = scope || window;
		for (var i = 0, j = this.length; i < j; ++i) {
			if(!fn.call(scope, this[i], i, this)) {
				return false
			}
		}
		return true
	}
}

if (!Array.prototype.filter) {
	Array.prototype.filter = function (fn, scope) {
		var scope = scope || window;
		var a = [];
		for (var i = 0, j = this.length; i < j; ++i) {
			if(!fn.call(scope, this[i], i, this)) {
				continue
			}
			a.push(this[i])
		}
		return a
	}
}

if (!Array.prototype.forEach) {
	Array.prototype.forEach = function (fn, scope) {
		var scope = scope || window;
		for (var i = 0, j = this.length; i < j; ++i) {
			fn.call(scope, this[i], i, this)
		}
	}
}

if (!Array.prototype.map) {
	Array.prototype.map = function (fn, scope) {
		var scope = scope || window;
		var a = [];
		for (var i = 0, j = this.length; i < j; ++i) {
			a.push(fn.call(scope, this[i], i, this))
		}
		return a
	}
}

if (!Array.prototype.some) {
	Array.prototype.some = function (fn, scope) {
		var scope = scope || window;
		for (var i = 0, j = this.length; i < j; ++i) {
			if (fn.call(scope, this[i], i, this)) {
				return true
			}
		}
	return false
	}
}