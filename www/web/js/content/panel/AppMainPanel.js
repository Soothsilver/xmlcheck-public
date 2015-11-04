/**
 * Main content switcher for the main part of the application (accessible after
 * login).
 * Contains top panel, left menu, and content area.
 */
asm.ui.panel.AppMainPanel = asm.ui.Container.extend({
	constructor: function (config) {
		this._errorPanel = new asm.ui.ContentPanel().extend({
			_buildContent: function () {
				this._errorContainer = $('<div></div>')
					.addClass('errorContainer')
					.appendTo(this.config.target);
			},
			getErrorContainer: function () {
				return this._errorContainer;
			}
		});

		var thisMainPanel = this;
		var errorLog = new asm.ui.table.ErrorLog().extend({
			_adjustContent: function () {
				this.fill(this._translateErrors(thisMainPanel._errorManager.getLog()));
			}
		});

		var defaults = {
			children: {
				topBar: new asm.ui.panel.AppTopPanel({classes: ['app-top']}),
				leftMenu: this._createLeftMenu(),
				contentArea: new asm.ui.Container({children: {
					errors: this._errorPanel,
					content: new asm.ui.ContentSwitcher({children: {
						'': new asm.ui.panel.Home(),

						studentAssignments: new asm.ui.panel.Assignments(),
						submissions: new asm.ui.panel.Submissions(),
						subscriptions: new asm.ui.panel.Subscriptions(),

                        correctionSeparated: new asm.ui.panel.CorrectionWithSeparatedAssignments(),
						correctionAll: new asm.ui.panel.Correction(),
						correctionAbsolutelyAll: new asm.ui.table.CorrectionAbsolutelyAll(),

						submissionDetails: new asm.ui.panel.SubmissionDetails(),

						requests: new asm.ui.table.SubscriptionRequests({
							actions: {
								extra: [{
									expire: [asm.ui.globals.stores.subscriptionRequests, asm.ui.globals.stores.subscriptions],
									icon: 'ui-icon-' + asm.ui.globals.icons.confirm,
									label: asm.lang.subscriptions.permitRequest,
									request: 'PermitSubscription',
									refresh: true
								}, {
									expire: [asm.ui.globals.stores.subscriptionRequests, asm.ui.globals.stores.subscriptions],
									icon: 'ui-icon-' + asm.ui.globals.icons.cancel,
									label: asm.lang.subscriptions.prohibitRequest,
									request: 'ProhibitSubscription',
									refresh: true
								}]
							}
						}),
						userRatings: new asm.ui.panel.UserRatingTables(),
                        groups: new asm.ui.editor.Groups(),
                        assignments: new asm.ui.editor.Assignments(),

						lectures: new asm.ui.editor.Lectures(),
						problems: new asm.ui.editor.Problems(),
						questions: new asm.ui.panel.Questions(),
						attachments: new asm.ui.editor.Attachments(),
						tests: new asm.ui.table.Tests(),
                        plugins: new asm.ui.editor.Plugins(),
                        pluginTests: new asm.ui.panel.PluginTests(),

						users: new asm.ui.editor.Users(),
						usertypes: new asm.ui.editor.Usertypes(),
                        changelog: new asm.ui.panel.Changelog(),
						errorLog: errorLog,
                        otherAdministration: new asm.ui.form.OtherAdministration(),

						userSettings: new asm.ui.form.UserAccount({
							callbacks: {
								success: $.proxy(function (response, data) {

									this._requestAdjust();
                                    if (data.pass.length > 0)
                                    {
                                        this._triggerError(new asm.ui.Error(asm.lang.home.passwordChanged, asm.ui.Error.NOTICE));
                                    }
                                    else
                                    {
                                        this._triggerError(new asm.ui.Error(asm.lang.home.passwordNotChanged, asm.ui.Error.NOTICE));
                                    }
								}, this)
							},
							noCache: true
						}),
                        emailSettings: new asm.ui.form.EmailSettings(),
						uiSettings: new asm.ui.form.UiSettings(),
                        languageSettings : new asm.ui.form.LanguageSettings()
					}})
				}, classes: ['panel-rest']})
			}
		};
		this.base($.extend(defaults, config));

		this.bind('menu.switch', function (params, event) {
			event.stopPropagation();
			this._requestAdjust(params.value);
		}, this);

		this._bindContentSwitchHandlers();
		this._bindErrorHandler();
	},
	_showContent: function () {
		var session = asm.ui.globals.session,
			privileges = session.getProperty('privileges') || {};
		this.config.children.leftMenu.changeConfig({
			structure: this._getMenuStructure(privileges)
		});
		this.base();
	},
	_bindContentSwitchHandlers: function () {
		var index = {
			'lectures.showProblems': ['problems', 'lectureId'],
			'lectures.showQuestions': ['questions', 'lectureId'],
			'lectures.showAttachments': ['attachments', 'lectureId'],
			'lectures.showTests': ['tests', 'lectureId'],
			'groups.showAssignments': ['assignments', 'groupId'],
			'groups.showRatings': ['userRatings', 'groupId'],
			'assignments.showSubmissions': 'submissions',
			'ratings.showSubmissions': ['correction', 'userId'],
			'questions.showTests': 'tests',
			'tests.create': 'questions'
		};

		for (var eventName in index) {
			var props = $.isArray(index[eventName]) ? index[eventName] : [index[eventName]];

			this.bind(eventName, { redirect: props }, function (params, event) {
				event.stopPropagation();
				var args = [],
					childName = params.redirect[0],
					redirectParams = params.redirect.slice(1);
				for (var i in redirectParams) {
					args.push(params[redirectParams[i]]);
				}
				this._requestAdjust($.merge([childName], args));
			}, this);
		}
	},
	_bindErrorHandler: function () {
		this._errorManager = new asm.ui.PanelErrorManager({
			hideEffect: 'pulsate',
			hideEffectOptions: {
				times: 1,
				duration: 1600
			},
			showEffect: 'pulsate',
			showEffectOptions: {
				times: 2,
				duration: 400
			},
			timeout: 15
		});
		
		this.bind('panel.error', function (params, event) {
			if (params.error.getSeverity() < asm.ui.Error.FATAL) {
				event.stopPropagation();
				this._errorManager.setTarget(this._errorPanel.getErrorContainer());
				this._errorManager.add(params.error);
			}
		}, this);
	},
	_createLeftMenu: function () {
		return new asm.ui.AccordionMenuPanel({
			classes: ['panel-left'],
			icons: {
				assignments: asm.ui.globals.icons.assignment,
				attachments: asm.ui.globals.icons.attachment,
				correctionAll: asm.ui.globals.icons.submission,
                correctionSeparated: asm.ui.globals.icons.submission,
				correctionAbsolutelyAll: asm.ui.globals.icons.submission,
				errorLog: asm.ui.globals.icons.log,
				groups: asm.ui.globals.icons.group,
				lectures: asm.ui.globals.icons.lecture,
				plugins: asm.ui.globals.icons.plugin,
				pluginTests: asm.ui.globals.icons.test,
				problems: asm.ui.globals.icons.problem,
				questions: asm.ui.globals.icons.question,
				userRatings: asm.ui.globals.icons.user,
				requests: asm.ui.globals.icons.subscription,
				studentAssignments: asm.ui.globals.icons.assignment,
				submissions: asm.ui.globals.icons.submission,
				subscriptions: asm.ui.globals.icons.subscription,
				tests: asm.ui.globals.icons.xtest,
				uiSettings: asm.ui.globals.icons.settings,
				userSettings: asm.ui.globals.icons.account,
				users: asm.ui.globals.icons.user,
				usertypes: asm.ui.globals.icons.usertype,
                changelog: asm.ui.globals.icons.log,
                emailSettings: asm.ui.globals.icons.attachment,
                languageSettings: asm.ui.globals.icons.settings,
                otherAdministration: asm.ui.globals.icons.settings
			},
			noCache: true
		});
	},
	_getMenuStructure: function (privileges) {
		var pagePrivs = {
				assignments: ['groupsManageAll', 'groupsManageOwn'],
				attachments: ['lecturesManageAll', 'lecturesManageOwn'],
				correctionAll: ['submissionsCorrect'],
                correctionSeparated: ['submissionsCorrect'],
				correctionAbsolutelyAll: [ 'groupsManageAll', 'lectureManageAll', 'otherAdministration'],
				groups: ['groupsAdd', 'groupsManageAll', 'groupsManageOwn'],
				lectures: ['lecturesAdd', 'lecturesManageAll', 'lecturesManageOwn'],
				plugins: ['pluginsExplore'],
				pluginTests: ['pluginsTest'],
				problems: ['lecturesManageAll', 'lecturesManageOwn'],
				questions: ['lecturesManageAll', 'lecturesManageOwn'],
				studentAssignments: ['assignmentsSubmit'],
				submissions: ['assignmentsSubmit'],
				subscriptions: ['assignmentsSubmit'],
				requests: ['groupsAdd', 'groupsManageAll', 'groupsManageOwn'],
				tests: ['lecturesManageAll', 'lecturesManageOwn'],
				users: ['usersExplore'],
				usertypes: ['usersPrivPresets'],
				userRatings: ['groupsManageAll', 'groupsManageOwn', 'submissionsCorrect'],
                otherAdministration: [ 'otherAdministration' ]
			},
			baseStructure = {
				'student': {
					'studentAssignments': asm.lang.menu.studentAssignments,
					'submissions': asm.lang.menu.submissions,
					'subscriptions': asm.lang.menu.subscriptions
				},
				'tutor': {
					'assignments': asm.lang.menu.assignments,
					'groups': asm.lang.menu.groups,
                    'correctionAll': asm.lang.menu.correctionAll,
					'correctionAbsolutelyAll' : asm.lang.menu.correctionAbsolutelyAll,
					'userRatings': asm.lang.menu.userRatings,
					'requests': asm.lang.menu.subscriptionRequests
				},
				'lecturer': {
					'lectures': asm.lang.menu.lectures,
					'problems': asm.lang.menu.problems,
					'plugins': asm.lang.menu.plugins,
					'pluginTests': asm.lang.menu.pluginTests,
					'questions': asm.lang.menu.questions,
					'attachments': asm.lang.menu.attachments,
					'tests': asm.lang.menu.tests
				},
				'system': {
                    'changelog' : asm.lang.menu.changelog,
					'errorLog': asm.lang.menu.uiLog,
					'users': asm.lang.menu.users,
					'usertypes': asm.lang.menu.userTypes,
                    'otherAdministration': asm.lang.menu.otherAdministration
				},
				'settings': {
					'userSettings': asm.lang.menu.accountSettings,
                    'emailSettings': asm.lang.menu.emailNotification,
					'uiSettings': asm.lang.menu.userInterface,
                    'languageSettings' : asm.lang.menu.languageSettings
				}
			},
			structure = $.extend({}, baseStructure);

		for (var i in baseStructure) {
			var group = baseStructure[i],
				showGroup = false;

			for (var j in group) {
				var show = asm.ui.Utils.matchCNF(pagePrivs[j] || [], function (priv, privileges) {
						return privileges[priv] || false;
					}, privileges);
				if (show) {
					showGroup = true;
				} else {
					delete structure[i][j];
				}
			}
			
			if (!showGroup) {
				delete structure[i];
			}
		}

		return structure;
	},
	clearErrors: function () {
		this._errorManager.clear();
	}
});