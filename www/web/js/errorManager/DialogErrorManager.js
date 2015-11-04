/**
 * @copybrief ErrorManager
 * 
 * Displays errors as modal dialog windows.
 */
asm.ui.DialogErrorManager = asm.ui.ErrorManager.extend({
	/**
	 * Shows supplied error in an appropriately styled dialog.
	 * @tparam mixed error
	 */
	_show: function (error) {
		var title = null,
		showFn = '*Unknown function to call*';
		switch (error.getSeverity()) {
			case asm.ui.Error.FATAL:
                showFn = 'error';
				title = asm.lang.errors.fatalError;
				break;
            case asm.ui.Error.ERROR:
                showFn = 'error';
                title = asm.lang.errors.error;
                break;
			case asm.ui.Error.WARNING:
                showFn = 'warning';
				title = asm.lang.errors.warning;
				break;
			case asm.ui.Error.NOTICE:
                showFn = 'notice';
                title = asm.lang.errors.notice;
		}
		asm.ui.globals.dialogManager[showFn](error.toString(), title, function () {
			this._hide(error);
		}, this);
	}
});