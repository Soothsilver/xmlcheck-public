/**
 * @file
 * Launches @projectname GUI in web browser.
 *
 * Attempts following actions:
 * @li show "unsupported browser" content and stop in unsupported browsers
 * @li create global dialog manager instance
 * @li create global core communicator instance
 * @li create global file downloader instance
 * @li create global application configuration instance and binds GUI skin change
 *		to change of @c theme property
 * @li create global user session manager instance
 * @li apply base application skin to page body
 * @li create and show application content (asm.ui.AppRootPanel)
 */
$(document).ready(function () {
	document.title = asm.ui.globals.appName;

    // If the client uses MSIE8 or older, or an unknown browser, prevent access and show the "Unsupported" message.
	var browser = null;
	var browserLinks = [];
	for (var i in asm.ui.globals.supportedBrowsers) {
		var props = asm.ui.globals.supportedBrowsers[i];
		if (!browser && $.browser[props.flag] &&
				(!props.version || props.version.test($.browser.version))) {
			browser = props;
		}
		browserLinks.push('<a href="' + props.link + '">' + props.name + '</a>');
	}
	if (browser) {
		if (browser.style) {
			$('#browserStylesheet', 'head').attr('href', './web/css/browser/' + browser.style + '.css');
		}
	} else {
		$('<div></div>').addClass('browserNotSupported')
			.append(asm.ui.globals.appName + ' currently doesn\'t support your web browser. Supported browsers so far include '
					+ browserLinks.join(', ') + '.')
			.appendTo('body');
		return;
	}

	asm.ui.globals.dialogManager = new asm.ui.DialogManager();

	asm.ui.globals.coreCommunicator = new asm.ui.JsonCoreCommunicator({
		url: asm.ui.globals.coreUrl
	});

	var fileDownloaderConfig = {
		url: asm.ui.globals.coreUrl,
		resultHandler: function (args, myWindow) {
			asm.ui.globals.coreCommunicator.apply(
					asm.ui.globals.coreCommunicator, args);
		}
	};
	asm.ui.globals.fileSaver = new asm.ui.FileSaver(fileDownloaderConfig);
	asm.ui.globals.fileViewer = new asm.ui.FileViewer(fileDownloaderConfig);
	asm.ui.globals.filePrinter = new asm.ui.FileSaver({
		url: asm.ui.globals.coreUrl,
		resultHandler: function printResult (args, myWindow) {
			args[0] = args[0].replace(/^<pre[^>]*>/, '').replace(/<\/pre>?/, '');
			if (/^\s*\{/.test(args[0])) {
				asm.ui.globals.coreCommunicator.handleResult.apply(
						asm.ui.globals.coreCommunicator, args);
			} else {
				if ($.browser.mozilla) {
					myWindow.focus();
					myWindow.contentWindow.print();
				} else {
					if ($.browser.safari && $.browser.webkit) {
						if (printResult.chromeTimeout) {
							asm.ui.globals.dialogManager.notice('Google Chrome prevents JavaScript from trying to print too often. Please wait or try using a different web browser.', 'Direct printing problem');
							return;
						} else {
							printResult.chromeTimeout = true;
							window.setTimeout(function () {
								printResult.chromeTimeout = false;
							}, 35 * 1000);
						}
					}
					myWindow.contentWindow.focus();
					myWindow.contentWindow.print();
				}
			}
		}
	});

	asm.ui.globals.config = new asm.ui.Config({
		defaults: asm.ui.globals.defaults,
		expires: 365
	});


	asm.ui.globals.config.bindChange('theme', function (theme) {
		$('#themeStylesheet', 'head').attr('href',
            ($.ui.version === '1.8.23' ? './web/lib/themes/'  : './web/lib/css/jquery-ui/')
                + theme + '/jquery-ui.css');
	});
	asm.ui.globals.config.triggerChange('theme');

	asm.ui.globals.session = new asm.ui.Session({
		interval: 5 * 60 * 1000, // five minutes
		timeout: asm.ui.globals.sessionTimeout
	});

	var page = $('<div></div>').attr('id', 'page')
		.appendTo('body');


	$(window).resize(function () {
		page.height($(window).height());
	}).resize();


	var application = new asm.ui.panel.AppRootPanel({
		classes: ['app-container'],
		target: page
	});

	var initialHash = window.location.hash.split('#');
	initialHash.shift();
	application.show(initialHash);
});