/**
 * Application top panel (logo, title, username, logout button).
 */
asm.ui.panel.AppTopPanel = asm.ui.ContentPanel.extend({
	_buildContent: function () {
		var o = this.config;

		var content = $('<div></div>').addClass('ui-widget-header')
			.appendTo(o.target)
			.corner();



		this._buildButton($.proxy(function () {
				this.trigger('custom.logout');
			}, this), asm.lang.menu.logout, 'ui-icon-' + asm.ui.globals.icons.logout)
			.attr('id', 'logout')
			.appendTo(content);



		var loginInfo = $('<div></div>').attr('id', 'login-info')
			.addClass('ui-labeled-icon')
			.append($('<span></span>')
				.icon({ type: 'person' }))
			.appendTo(content);

		this._userInfo = $('<span></span>').appendTo(loginInfo);
			
		var appTitle = $('<a></a>').attr('href', '#')
				.appendTo(content);

		$('<div></div>').attr('id', 'app-logo')
			.appendTo(appTitle);
			
		var appTitleEl = $('<h1></h1>').attr('id', 'app-title')
			.append(asm.ui.globals.appName + ' ')
			.appendTo(appTitle);

		var appVersionEl = $('<span></span>')
			.appendTo(appTitleEl);
			
		asm.ui.globals.coreCommunicator.request('GetVersion', function (data) {
			appVersionEl.text(data.version);
		});
	},
	_adjustContent: function () {
		this._userInfo.html(asm.ui.globals.session.getProperty('name') || '[not logged in]');
	}
});