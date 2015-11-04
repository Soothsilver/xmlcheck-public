/**
 * Table with GUI errors.
 */
asm.ui.table.ErrorLog = asm.ui.DynamicTable.extend({
	constructor: function (config) {
		var defaults = {
			icon: asm.ui.globals.icons.log,
			noCache: true,
			structure: {
				timestamp: { label: asm.lang.uiLog.timestamp, comparable: true, string: true },
				severity: { label: asm.lang.uiLog.type, comparable: true },
				message: { label: asm.lang.uiLog.message, comparable: true, string: true },
				details: { label: asm.lang.uiLog.details, string: true }
			},
			title: asm.lang.uiLog.caption
		};
		this.base($.extend(defaults, config));
	},
	_translateSeverity: function (severity) {
		switch (severity) {
			case asm.ui.Error.FATAL:
				return asm.lang.uiLog.fatal;
			case asm.ui.Error.ERROR:
				return asm.lang.uiLog.error;
			case asm.ui.Error.WARNING:
				return asm.lang.uiLog.warning;
			default:
				return asm.lang.uiLog.notice;
		}
	},
	_translateErrors: function (errors) {
		var translated = [];
		for (var i in errors) {
			var error = errors[i],
				detailsHash = error.getDetails(),
				details = [];
			for (var key in detailsHash) {
				var value = detailsHash[key];
				if (typeof value == 'object') {
					value = asm.ui.ObjectUtils.toString(value, true);
				}
				details.push(key + ': ' + value);
			}
			translated.push({
				details: details.join('\n'),
				message: error.toString(),
				severity: this._translateSeverity(error.getSeverity()),
				timestamp: error.getTimestamp()
			});
		}
		return translated;
	}
});