/**
 * Manages downloading files and viewing them in a new tab/window.
 */
asm.ui.FileViewer = asm.ui.FileDownloader.extend({
	_createWindow: function (url) {
		return window.open(url, 'asm.ui.FileViewer', 'location=no,menubar=no,status=no', true);
	}
});