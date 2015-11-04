<?php
/**
 * @file
 * Command-line script for transforming PHP source file with asm::docs::PhpInputFilter.
 * Accepts single argument (path to source file).
 */

use asm\docs\InputFilterScript;
use asm\docs\PhpInputFilter;

require_once __DIR__ . "/filter/InputFilterScript.php";
require_once __DIR__ . "/filter/PhpInputFilter.php";

$script = new InputFilterScript(new PhpInputFilter());
$script->run($argc, $argv);

