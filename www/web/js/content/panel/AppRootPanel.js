/**
 * Root content switcher of the whole application.
 * Uses @ref Navigator for transparent page navigation. Contains four children
 * @c login, @c register, @c activate, and main application container. Uses
 * DialogErrorManager to display errors not caught in children.
 */
asm.ui.panel.AppRootPanel = asm.ui.ContentSwitcher.extend({
	constructor: function (config) {
		this._mainPanel = new asm.ui.panel.AppMainPanel();

		var backToLoginButton = {
			label: asm.lang.loginScreen.back,
			icon: 'ui-icon-' + asm.ui.globals.icons.back,
			action: this._makeRedirectCallback('login')
		};

		var defaults = {
			children: {
				login: new asm.ui.DocLinkWrapper({
					children: {
						content: new asm.ui.form.Login({
							callbacks: {
								success: $.proxy(this._login, this)
							},
							classes: ['content-login'],
							formProps: {
								buttons: {
									register: {
										label: asm.lang.loginScreen.register,
										icon: 'ui-icon-' + asm.ui.globals.icons.register,
										action: this._makeRedirectCallback('register')
									},
									activate: {
										label: asm.lang.loginScreen.activate,
										icon: 'ui-icon-' + asm.ui.globals.icons.activate,
										action: this._makeRedirectCallback('activate')
									},
                                    lostPassword: {
                                        label: asm.lang.loginScreen.lostPassword,
                                        icon: 'ui-icon-' + asm.ui.globals.icons.lostPassword,
                                        action: this._makeRedirectCallback('lostPassword')
                                    }
								}
							},
							noCache: true
						})
					}
				}),
				register: new asm.ui.form.Register({
					callbacks: {
						success: $.proxy(function () {
							asm.ui.globals.dialogManager.notice(asm.lang.loginScreen.registrationSuccessful, asm.lang.general.success, function () {
								this.redirect('activate');
							}, this);
						}, this)
					},
					classes: ['content-register'],
					formProps: {
						buttons: {
							back: backToLoginButton
						}
					},
					noCache: true
				}),
				activate: new asm.ui.form.Activate({
					callbacks: {
						success: $.proxy(function () {
							asm.ui.globals.dialogManager.notice(asm.lang.loginScreen.activationSuccessful, asm.lang.general.success, function () {
								this.redirect('login');
							}, this);
						}, this)
					},
					classes: ['content-login'],
					formProps: {
						buttons: {
							back: backToLoginButton
						}
					},
					noCache: true
				}),
                resetPassword: new asm.ui.form.ResetPassword({
                    callbacks: {
                        success: $.proxy(function () {
                            asm.ui.globals.dialogManager.notice(asm.lang.loginScreen.passwordResetSuccessful, asm.lang.general.success, function () {
                                this.redirect('login');
                            }, this);
                        }, this)
                    },
                    noCache: true,
                    formProps: {
                        buttons: {
                            back: backToLoginButton
                        }
                    }
                }),
                lostPassword: new asm.ui.form.LostPassword({
                    callbacks: {
                      success: $.proxy(function (data) {
                          if (data.count == 1)
                          {
                             asm.ui.globals.dialogManager.notice(asm.lang.lostPasswordScreen.resetLinkSent, asm.lang.general.success);
                          }
                          else
                          {
                              asm.ui.globals.dialogManager.notice(asm.lang.lostPasswordScreen.resetLinksSent, asm.lang.general.success);
                          }
                       }, this)
                    },
                    noCache: true,
                    formProps: {
                        buttons: {
                            back: backToLoginButton
                        }
                    }
                }),
				'': this._mainPanel
			}
		};

		this.base($.extend(defaults, config));

		var errorManager = new asm.ui.DialogErrorManager();

		this.bind('panel.error', function (params) {
			errorManager.add(params.error);
		});

		asm.ui.globals.session.bind('requestUpdate', function () {
			asm.ui.globals.coreCommunicator.request('RefreshSession', function (data) {
				asm.ui.globals.session.refresh(data.timeout * 1000 - asm.ui.TimeUtils.time());
			});
		});

		asm.ui.globals.session.bind('expire', function () {
			this._logout(true);
		}, this);

		this._navigator = new asm.ui.Navigator();
		this._navigator.bind('hashchange', function (params) {
			this.show(params.stack);
		}, this);
		this._navigator.init();

		this._mainPanel.bind('custom.logout', function () {
            this._params = [];
			this._logout();
		}, this);
		this._mainPanel.bind('goToSubmissionDetails', function(submissionId) {
			this._navigator.redirect('submissionDetails', submissionId.newId);
		}, this);
		this._mainPanel.bind('goToUsersSubmissions', function(userId) {
			this._navigator.redirect('correctionAbsolutelyAll', userId.newId);
		}, this);
		this._mainPanel.bind('panel.adjustRequest', function (params) {
			this.redirect.apply(this, params.params);
		}, this);
	},
	_adjustContent: function () {
		var nextChild = this._selectNextChild();
		if ((nextChild == '') && !asm.ui.globals.session.isValid()) {
			this._logout(true);
			return;
		}

        if (nextChild === 'login' && asm.ui.globals.session.isValid())
        {
            this.redirect();
        }
        else
        {
		    this._switchContent(nextChild);
        }
	},
	_makeRedirectCallback: function () {
		var self = this,
			args = $.makeArray(arguments);
		return function () {
			self.redirect.apply(self, args);
		};
	},
	redirect: function () {
		return this._navigator.redirect.apply(this._navigator, arguments);
	},
	_login: function (data) {
		var timeout = data.timeout * 1000 - asm.ui.TimeUtils.time();
		delete data.timeout;

		asm.ui.globals.session.init(data);
		asm.ui.globals.session.refresh(timeout);

        if (this._params[1] === "thenTo" && this._params[0] === "login")
        {
            this.redirect.apply(this, this._params.slice(2));
        }
        else
        {
	    	this.redirect();
        }
	},
	_logout: function (local) {
		if (!local) {
			asm.ui.globals.coreCommunicator.request('Logout');
		}
		
		asm.ui.globals.session.destroy();
		for (var i in asm.ui.globals.stores) {
			asm.ui.globals.stores[i].empty();
		}
		this._mainPanel.clearErrors();
        if (this._params.length > 0)
        {
            var targetLoginScreen = [ 'login', 'thenTo' ].concat(this._params);
            this.redirect.apply(this, targetLoginScreen);
        }
        else
        {
            this.redirect(['login']);
        }
	}
});