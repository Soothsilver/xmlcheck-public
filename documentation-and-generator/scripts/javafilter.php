<?php
/**
 * @file
 * Command-line script for transforming Java source file with asm::docs::JavaInputFilter.
 * Accepts single argument (path to source file).
 */

use asm\docs\InputFilterScript;
use asm\docs\JavaInputFilter;

require_once __DIR__ . "/filter/InputFilterScript.php";
require_once __DIR__ . "/filter/JavaInputFilter.php";

$script = new InputFilterScript(new JavaInputFilter());
$script->run($argc, $argv);

