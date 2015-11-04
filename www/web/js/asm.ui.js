/**
 * @file
 * Namespace and global variable declarations.
 *
 * @namespace asm
 * Namespace for the whole @projectname project.
 *
 * @namespace asm::ui
 * Contains all @projectname GUI classes and "global" variables.
 *
 * @namespace asm::ui::editor
 * Contains all @ref asm.DynamicTableEditor "table editor" panels.
 *
 * @namespace asm::ui::form
 * Contains all @ref asm.DynamicForm "form" panels.
 *
 * @namespace asm::ui::panel
 * Contains all miscellaneous panels (special content panels and containers).
 * 
 * @namespace asm::ui::table
 * Contains all @ref asm.DynamicTable "table" panels.
 */
asm = window.asm || {};
asm.ui = asm.ui || {};
asm.ui.editor = asm.ui.editor || {};
asm.ui.form = asm.ui.form || {};
asm.ui.panel = asm.ui.panel || {};
asm.ui.table = asm.ui.table || {};

asm.ui.globals = {
	stores: {},
	appName: 'XML Check',
	coreUrl: './core/request.php',
	defaults: {
		theme: 'ui-lightness'
	},
	icons: {
		account: 'contact',
		activate: 'scissors',
		attachment: 'contact',
		add: 'plus',
		assignment: 'calendar',
		back: 'arrowreturnthick-1-w',
		cancel: 'close',
		confirm: 'check',
		create: 'plusthick',
		'delete': 'trash',
		downloadInput: 'circle-arrow-n',
		downloadOutput: 'circle-arrow-s',
		check: 'circle-check',
		edit: 'pencil',
		group: 'link',
		lecture: 'bookmark',
		log: 'script',
		login: 'key',
		logout: 'locked',
		plugin: 'gear',
		print: 'print',
		problem: 'document',
		question: 'comment',
		rating: 'tag',
		register: 'plusthick',
		results: 'script',
		settings: 'wrench',
		subscription: 'signal-diag',
		submissionDraft: 'mail-open',
		submission: 'mail-closed',
		submissionRated: 'mail-open',
		test: 'clock',
		user: 'person',
		usertype: 'key',
		xtest: 'document-b',
		xtestGenerate: 'refresh',
        lostPassword: 'key',
		submissionDetails: 'copy'
	},
	sessionTimeout: 2 * 60 * 60 * 1000, // 2 hours
	themes: ['black-tie', 'blitzer', 'cupertino', 'dark-hive', 'dot-luv', 'eggplant',
		'excite-bike', 'flick', 'hot-sneaks', 'humanity', 'le-frog', 'mint-choc',
		'overcast', 'pepper-grinder', 'redmond', 'smoothness', 'south-street',
		'start', 'sunny', 'swanky-purse', 'trontastic', 'ui-darkness', 'ui-lightness',
		'vader'],
	privilegesBreakdown: {
		users: {
			add: ['usersAdd', 'plus', asm.lang.usertypes.users_add],
			explore: ['usersExplore', 'search', asm.lang.usertypes.users_explore],
			edit: ['usersManage', 'pencil', asm.lang.usertypes.users_editUsers],
			remove: ['usersRemove', 'trash', asm.lang.usertypes.users_remove],
			'edit types of': ['usersPrivPresets', 'wrench', asm.lang.usertypes.usertypes_edit]
		},
		subscriptions: {
			'public': ['groupsJoinPublic', 'unlocked', asm.lang.usertypes.subscriptions_joinpublic],
			'request private': ['groupsRequest', 'comment', asm.lang.usertypes.subscriptions_requestprivate],
			'private': ['groupsJoinPrivate', 'locked', asm.lang.usertypes.subscriptions_joinprivate]
		},
		plugins: {
			add: ['pluginsAdd', 'plus', asm.lang.usertypes.plugins_add],
			explore: ['pluginsExplore', 'search', asm.lang.usertypes.plugins_explore],
			edit: ['pluginsManage', 'pencil', asm.lang.usertypes.plugins_edit],
			remove: ['pluginsRemove', 'trash', asm.lang.usertypes.plugins_remove],
			test: ['pluginsTest', 'gear', asm.lang.usertypes.plugins_test]
		},
		assignments: {
			submit: ['assignmentsSubmit', 'mail-closed', asm.lang.usertypes.assignments_submit]
		},
		submissions: {
			correct: ['submissionsCorrect', 'tag', asm.lang.usertypes.submissions_grade],
			'view authors of': ['submissionsViewAuthors', 'person', asm.lang.usertypes.submissions_viewAuthors],
			're-rate': ['submissionsModifyRated', 'pencil', asm.lang.usertypes.submissions_regrade]
		},
		lectures: {
			add: ['lecturesAdd', 'plus', asm.lang.usertypes.lectures_add],
			'edit own': ['lecturesManageOwn', 'pencil', asm.lang.usertypes.lectures_editOwn],
			'edit all': ['lecturesManageAll', 'note', asm.lang.usertypes.lectures_editAll]
		},
		groups: {
			add: ['groupsAdd', 'plus', asm.lang.usertypes.groups_add],
			'edit own': ['groupsManageOwn', 'pencil', asm.lang.usertypes.groups_editOwn],
			'edit all': ['groupsManageAll', 'note', asm.lang.usertypes.groups_editAll]
		},
		other: {
			'other administration': ['otherAdministration', 'script', asm.lang.usertypes.other_administration]
		}
	},
	supportedBrowsers: {
        // This is how jMigrate plugin reports Chrome now.
		chrome: {
			name: 'Google Chrome',
			flag: 'chrome',
			style: 'webkit',
			link: 'http://www.google.com/chrome'
		},
        // This is how jQuery reported Chrome previously.
        webkit: {
            name: 'Google Chrome (as webkit)',
            flag: 'webkit',
            style: 'webkit',
            link: 'http://www.google.com/chrome'
        },
		firefox: {
			name: 'Mozilla Firefox',
			flag: 'mozilla',
			style: 'mozilla',
			link: 'http://www.mozilla.com/firefox'
		},
		opera: {
			name: 'Opera',
			flag: 'opera',
			link: 'http://www.opera.com/download/'
		},
		msie: {
			name: 'Internet Explorer 9+',
			flag: 'msie',
			version: /^9./,
			link: 'http://www.beautyoftheweb.com/'
		}
	},
	selectedIds: [] // array of selected questions for test generation
};