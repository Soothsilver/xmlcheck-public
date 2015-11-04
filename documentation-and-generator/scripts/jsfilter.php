<?php
error_reporting(E_ERROR);
// ^ This is because E_NOTICE-level errors in the JsInputFilter.php prevent the Doxygen generator from functioning otherwise.
// I don't understand the code in JsInputFilter.php enough to fix the errors.

/**
 * @file
 * Command-line script for transforming JavaScript source file with asm::docs::JsInputFilter.
 * Accepts single argument (path to source file).
 */
require_once __DIR__ . "/filter/InputFilterScript.php";
require_once __DIR__ . "/filter/InputFilterSet.php";
require_once __DIR__ . "/filter/BaseJsInputFilter.php";
require_once __DIR__ . "/filter/ExtensionJsInputFilter.php";
require_once __DIR__ . "/filter/WidgetJsInputFilter.php";

use
	asm\docs\InputFilterScript,
	asm\docs\InputFilterSet,
	asm\docs\BaseJsInputFilter,
	asm\docs\ExtensionJsInputFilter,
	asm\docs\WidgetJsInputFilter;


$script = new InputFilterScript(
		    new InputFilterSet(
			 new BaseJsInputFilter,
			 new ExtensionJsInputFilter,
			 new WidgetJsInputFilter
			));
$script->run($argc, $argv);

?>