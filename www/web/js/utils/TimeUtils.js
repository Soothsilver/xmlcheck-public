/**
 * Time-oriented utility functions @module.
 */
asm.ui.TimeUtils = Base.extend({
	constructor: null
}, {
	/**
	 * Gets number of miliseconds since the start of UNIX epoch.
	 * @treturn int
	 */
	time: function () {
		return +new Date();
	},
	/**
	 * Formats time like MySQL TIMESTAMP.
	 * @tparam int time @optional time in ms since epoch start (defaults to current
	 *		time)
	 * @treturn string datetime in following format: <tt>YYYY-MM-DD hh:mm:ss</tt>
	 * @see time()
	 */
	mysqlTimestamp: function (time) {
		if (time == undefined) {
			time = this.time();
		}
		var date = new Date(time),
			d = {
				year: date.getFullYear(),
				month: date.getMonth() + 1,
				day: date.getDate(),
				hours: date.getHours(),
				minutes: date.getMinutes(),
				seconds: date.getSeconds()
			};
		for (var i in d) {
			if (d[i] < 10) {
				d[i] = '0' + d[i];
			}
		}

		return (d.year + '-' + d.month + '-' + d.day
			+ ' ' + d.hours + ':' + d.minutes + ':' + d.seconds);
	}
});