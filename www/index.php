<?php
function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}
function endsWith($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}
function combinePaths($directory, $file)
{
    if (!endsWith($directory, "/"))
    {
        $directory .= "/";
    }
    if (startsWith($file, "/"))
    {
        $file = substr($file, 1);
    }
    return $directory . $file;
}
function echoScripts($directory)
{
    $scriptFiles = array_diff(scandir($directory), array('..', '.'));
    foreach ($scriptFiles as $scriptFile) {
        if (endsWith($scriptFile, ".js"))
        {
            $path = combinePaths($directory, $scriptFile);
            echo "\t<!--suppress HtmlUnknownTarget --><script type=\"text/javascript\" src=\"{$path}\"></script>\n";
        }
    }


}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <!-- ~~~~~~~~~~~~~~~~~~~~~ Metadata ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    
    <!-- ~~~~~~~~~~~~~~~~~~~~~ Page title ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <title>Assignment Manager (Loading...)</title>
    
    <!-- ~~~~~~~~~~~~~~~~~~~~~ Favicon ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <link rel="shortcut icon" href="web/images/favicon.ico"/>

    <!-- ~~~~~~~~~~~~~~~~~~~~~ Styles ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <link rel="stylesheet" type="text/css" href="web/css/reset.css"/>
    <link rel="stylesheet" type="text/css" id="themeStylesheet"/>
    <link rel="stylesheet" type="text/css" href="web/css/widgets/cursor.css"/>
    <link rel="stylesheet" type="text/css" href="web/css/widgets/field.css"/>
    <link rel="stylesheet" type="text/css" href="web/css/widgets/fieldset.css"/>
    <link rel="stylesheet" type="text/css" href="web/css/widgets/form.css"/>
    <link rel="stylesheet" type="text/css" href="web/css/widgets/icon.css"/>
    <link rel="stylesheet" type="text/css" href="web/css/widgets/overlay.css"/>
    <link rel="stylesheet" type="text/css" href="web/css/widgets/panel.css"/>
    <link rel="stylesheet" type="text/css" href="web/css/widgets/progressIndicator.css"/>
    <link rel="stylesheet" type="text/css" href="web/css/widgets/progressbar.css"/>
    <link rel="stylesheet" type="text/css" href="web/css/widgets/table.css"/>
    <link rel="stylesheet" type="text/css" href="web/css/widgets/textCutter.css"/>
    <link rel="stylesheet" type="text/css" href="web/css/base.css"/>
    <link rel="stylesheet" type="text/css" href="web/css/layout.css"/>
    <link rel="stylesheet" type="text/css" href="web/css/content.css"/>
    <link rel="stylesheet" type="text/css" href="web/css/overrides.css"/>
    <link rel="stylesheet" type="text/css" id="browserStylesheet"/>
    <!-- Something may be wrong now because we have high jQuery version. -->
    <!-- ~~~~~~~~~~~~~~~~~~~~~ Libraries ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <script type="text/javascript" src="web/lib/js/Base.js"></script>
    <script type="text/javascript" src="web/lib/js/jquery-1.7.js"></script>
    <script type="text/javascript" src="web/lib/js/jquery-ui.js"></script>
    <script type="text/javascript" src="web/lib/js/jquery.animate.clip.js"></script>
    <script type="text/javascript" src="web/lib/js/jquery.json.js"></script>
    <script type="text/javascript" src="web/lib/js/webtoolkit.Aim.js"></script>

    <!-- ~~~~~~~~~~~~~~~~~~~~~ Hacks to third party libraries~~~~~~~~~~~~~ -->
    <link rel="stylesheet" type="text/css" href="web/css/jquery-ui-fix.css"/>
    <script type="text/javascript" src="web/lib/js/jquery-ui-fix.js"></script>

    <!-- ~~~~~~~~~~~~~~~~~~~~~ Extensions ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <script type="text/javascript" src="web/js/extensions/String.js"></script>
    <script type="text/javascript" src="web/js/extensions/jQuery.js"></script>
    <script type="text/javascript" src="web/js/extensions/jQuery.fn.js"></script>


    <!-- ~~~~~~~~~~~~~~~~~~~~~ Language ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <script type="application/javascript" src="web/js/utils/CookieUtils.js"></script>
    <!-- Requires CookieUtils -->
    <script type="text/javascript" src="web/js/asm.ui.constants.js"></script>
    <script type="application/javascript" src="web/js/lang/lang_cs.js"></script>
    <script type="application/javascript" src="web/js/lang/lang.js"></script>
    <script type="application/javascript" src="web/js/lang/choose_language.js"></script>

    <!-- ~~~~~~~~~~~~~~~~~~~~~ Application ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <script type="text/javascript" src="web/js/asm.ui.js"></script>

    <!-- Utils -->
    <?php echoScripts("./web/js/utils"); ?>


    <!-- ~~~~~~~~~~~~~~~~~~~~~ Widgets ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->

    <!-- Requires language -->
    <?php echoScripts("./web/js/widgets"); ?>

    <!-- Mix-In's -->
    <script type="text/javascript" src="web/js/mixin/Eventful.js"></script>
    <script type="text/javascript" src="web/js/mixin/Cached.js"></script>
    <script type="text/javascript" src="web/js/mixin/CookieCached.js"></script>

    <!-- Builder -->
    <script type="text/javascript" src="web/js/mixin/builder/Builder.js"></script>
    <script type="text/javascript" src="web/js/mixin/builder/FormBuilder.js"></script>
    <script type="text/javascript" src="web/js/mixin/builder/TableBuilder.js"></script>
    <script type="text/javascript" src="web/js/mixin/builder/MenuBuilder.js"></script>

    <!-- Error -->
    <script type="text/javascript" src="web/js/error/Error.js"></script>
    <script type="text/javascript" src="web/js/error/ConnectionError.js"></script>
    <script type="text/javascript" src="web/js/error/CoreError.js"></script>

    <!-- Error Manager -->
    <script type="text/javascript" src="web/js/errorManager/ErrorManager.js"></script>
    <script type="text/javascript" src="web/js/errorManager/DialogErrorManager.js"></script>
    <script type="text/javascript" src="web/js/errorManager/PanelErrorManager.js"></script>

    <!-- Base (Uncategorized) Classes -->
    <script type="text/javascript" src="web/js/base/Lock.js"></script>
    <script type="text/javascript" src="web/js/base/Config.js"></script>
    <script type="text/javascript" src="web/js/base/Session.js"></script>
    <script type="text/javascript" src="web/js/base/DialogManager.js"></script>
    <script type="text/javascript" src="web/js/base/CoreCommunicator.js"></script>
    <script type="text/javascript" src="web/js/base/JsonCoreCommunicator.js"></script>
    <script type="text/javascript" src="web/js/base/Navigator.js"></script>
    <script type="text/javascript" src="web/js/base/FileDownloader.js"></script>
    <script type="text/javascript" src="web/js/base/FileSaver.js"></script>
    <script type="text/javascript" src="web/js/base/FileViewer.js"></script>
    <script type="text/javascript" src="web/js/base/Macros.js"></script>

    <!-- Store -->
    <script type="text/javascript" src="web/js/store/Store.js"></script>
    <script type="text/javascript" src="web/js/store/TableStore.js"></script>

    <!-- Panel -->
    <script type="text/javascript" src="web/js/panel/Panel.js"></script>
    <script type="text/javascript" src="web/js/panel/Container.js"></script>
    <script type="text/javascript" src="web/js/panel/ContentSwitcher.js"></script>
    <script type="text/javascript" src="web/js/panel/DynamicTableEditor.js"></script>
    <script type="text/javascript" src="web/js/panel/ContentPanel.js"></script>
    <script type="text/javascript" src="web/js/panel/MenuPanel.js"></script>
    <script type="text/javascript" src="web/js/panel/AccordionMenuPanel.js"></script>
    <script type="text/javascript" src="web/js/panel/DynamicContentPanel.js"></script>
    <script type="text/javascript" src="web/js/panel/DynamicForm.js"></script>
    <script type="text/javascript" src="web/js/panel/DynamicTable.js"></script>

    <!-- ~~~~~~~~~~~~~~~~~~~~~ Content ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->

    <!-- Stores -->
    <?php echoScripts("./web/js/content/stores"); ?>

    <!-- Tables -->
    <script type="text/javascript" src="web/js/content/table/CorrectionBase.js"></script>
    <?php echoScripts("./web/js/content/table"); ?>

    <!-- Forms -->
    <?php echoScripts("./web/js/content/form"); ?>

    <!-- Editors -->
    <?php echoScripts("./web/js/content/editor"); ?>

    <!-- Misc. panels -->
    <!-- Order matters: -->
    <script type="text/javascript" src="web/js/content/panel/AppTopPanel.js"></script>
    <script type="text/javascript" src="web/js/content/panel/AppMainPanel.js"></script>
    <script type="text/javascript" src="web/js/content/panel/AppRootPanel.js"></script>
    <script type="text/javascript" src="web/js/content/panel/Assignments.js"></script>
    <script type="text/javascript" src="web/js/content/panel/Correction.js"></script>
    <script type="text/javascript" src="web/js/content/panel/SubmissionDetails.js"></script>
    <script type="text/javascript" src="web/js/content/panel/DocLinkWrapper.js"></script>
    <script type="text/javascript" src="web/js/content/panel/Home.js"></script>
    <script type="text/javascript" src="web/js/content/panel/PluginTests.js"></script>
    <script type="text/javascript" src="web/js/content/panel/Questions.js"></script>
    <script type="text/javascript" src="web/js/content/panel/Submissions.js"></script>
    <script type="text/javascript" src="web/js/content/panel/Subscriptions.js"></script>
    <script type="text/javascript" src="web/js/content/panel/UserRatingTables.js"></script>
    <script type="text/javascript" src="web/js/content/panel/Changelog.js"></script>
    <script type="text/javascript" src="web/js/content/panel/CorrectionWithSeparatedAssignments.js"></script>

    <!-- ~~~~~~~~~~~~~~~~~~~~~ Starting point ~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <script type="text/javascript" src="web/js/launchAsmApplication.js"></script>

</head>
<body class="ui-widget ui-widget-content ui-border-none">
<noscript>
    Assignment Manager application needs JavaScript to run. Please enable JavaScript
    in your browser.
</noscript>
</body>
</html>
