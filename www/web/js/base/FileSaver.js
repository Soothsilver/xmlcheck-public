/**
 * Manages downloading of files from server and saving them to disk.
 */
asm.ui.FileSaver = asm.ui.FileDownloader.extend({
	constructor: function (config) {
		this.base(config);
		this.disposable = [];
	},
	_createWindow: function (url) {
		var id = asm.ui.Utils.getUniqueElementId(),
			frame = $('<iframe></iframe>')
				.attr('id', id)
				.attr('name', id)
				.attr('src', url)
				.css({
					position: 'absolute',
					top: '-5000px',
					left: '-5000px'
				})
				.appendTo('body');
		return frame.get();
	},
	/**
	 * Removes all temporary frames marked as disposable.
	 */
	cleanup: function () {
		var myWindow;
		while ((myWindow = this.disposable.shift())) {
			$(myWindow).remove();
		}
	},
	trash: function (myWindow) {
		this.disposable.push(myWindow);
	}
});