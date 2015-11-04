/**
 * Application "default page" content panel.
 * Contains info about the project and author and panel with pending tasks.
 */
asm.ui.panel.Home = asm.ui.ContentPanel.extend({
	constructor: function (config) {
		var defaults = {
			noCache: true
		};
		this.base($.extend(defaults, config));
	},
	_buildContent: function () {
		var panels = {
			home: {
				icon: 'home',
				text: asm.lang.home.noTasks
			},
            messageOfTheDay: {
                icon: 'home',
                text: asm.lang.home.noMotd
            },
			copyright: {
				text: '<span class="fakeIcon">&copy;</span> &nbsp2009-2010 &nbsp;<strong>Jan Konopásek</strong>, 2014-2015 <strong>Petr Hudeček</strong>'
			}
		};

		this._panels = {};
		for (var i in panels) {
			var o = panels[i];
			this._panels[i] = this._buildTag('div')
				.append(o.text)
				.appendTo(this.config.target)
				.panel({ icon: o.icon });
		}
		this._panels.home.addClass('labeledListSet');
	},
	_adjustContent: function () {
        // Message of the day:

        asm.ui.globals.stores.motd.get($.proxy(function(data) {
            this._panels.messageOfTheDay.html(asm.lang.home.motd+ "<br><br>" + data.motd);
        }, this));
        // Tasks requiring your attention and ratings
		var homePanel = this._panels.home,
			panelIsEmpty = true,
			appendList = $.proxy(function (title, items) {
				if (items.length) {
					if (panelIsEmpty) {
						homePanel.empty();
						panelIsEmpty = false;
					}
					this._buildTag('div').addClass('listLabel')
						.append(title)
						.appendTo(homePanel);
					var list = this._buildTag('ul').addClass('indentedList')
						.appendTo(homePanel);
					for (var i in items) {
						this._buildTag('li').append(items[i])
							.appendTo(list);
					}
				}
			}, this),
			privileges = asm.ui.globals.session.getProperty('privileges') || {},
			lastLogin = asm.ui.globals.session.getProperty('lastLogin'),
			notices = {
				assignments: {
					description: asm.lang.home.unconfirmedAssignments,
					filter: function (assignment) {
						return !assignment.submissionExists;
					},
					show: privileges.assignmentsSubmit,
                    link: 'studentAssignments',
					store: asm.ui.globals.stores.studentAssignments
				},
				correction: {
					description: asm.lang.home.unratedSubmissions,
					show: privileges.submissionsCorrect,
                    link: 'correction',
					store: asm.ui.globals.stores.correction
				},
				requests: {
					description: asm.lang.home.subscriptionRequests,
                    link: 'requests',
					show: (privileges.groupsAdd || privileges.groupsManageOwn || privileges.groupsManageAll),
					store: asm.ui.globals.stores.subscriptionRequests
				}
			},
			loading = 4,
			showNotices = $.proxy(function () {
				var tasks = [];
				for (var i in notices) {
					if (notices[i].count) {
						tasks.push('<a href="#' + notices[i].link + '"><strong>' + notices[i].count + '</strong> ' + notices[i].description + '</a>');
					}
				}
				appendList(asm.lang.home.tasksRequireAttention, tasks);
			}, this),
			tryDoneLoading = function () {
				if (loading == 0) {
					loading = -1;
					showNotices();
				}
			},
			oneLoaded = function () {
				--loading;
				tryDoneLoading();
			};

		if (privileges.assignmentsSubmit) {
			asm.ui.globals.stores.ratingsStudent.get(function (data) {
				if (data.length) {
					var ratings = [];
					for (var i in data) {
						ratings.push(data[i].group + ' (' + data[i].lecture + '): <strong>'
								+ data[i].rating + '</strong> ' + asm.lang.home.points);
					}
					appendList(asm.lang.home.currentGroupRatings, ratings);
				}
			});
		}

		for (var i in notices) {
			var o = notices[i];
			if (o.show) {
				o.store.get((function closure (props) {
					return function (data) {
						if (data === null) {
							props.count = 0;
							// Bez tohoto se stávala chyba u řešení požadavku "žádostí o členství k vyřešení".
						}
						else {
							props.count = props.filter ? $.grep(data, props.filter).length : data.length;
						}
						oneLoaded();
					};
				})(o));
			} else {
				oneLoaded();
			}
		}
		tryDoneLoading();
	}
});