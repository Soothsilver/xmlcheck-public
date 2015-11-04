asm.ui.panel.Submissions = asm.ui.Container.extend({
	constructor: function (config) {
		var defaults = {
			children: {
				normal: new asm.ui.table.SubmissionsNormal(),
				graded: new asm.ui.table.SubmissionsGraded()
			}
		};
		this.base($.extend(defaults, config));

		this.config.children.normal.bind('panel.refresh', function () {
			this.config.children.graded.refresh();
		}, this);
	}
});