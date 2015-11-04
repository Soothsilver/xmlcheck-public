<?php
namespace asm\unittests;

use asm\core\Config;
use asm\utils\Filesystem;

$xmlcheckRoot = "../../www";
require_once $xmlcheckRoot . "/vendor/autoload.php";

Config::init(Filesystem::combinePaths($xmlcheckRoot, "core/config.ini"), Filesystem::combinePaths($xmlcheckRoot, "core/internal.ini"));

class CheckerRunner {
    public static $xmlCheckRoot = "../../www";
    public static $testCasesRoot = "../plugins/cases";
    /**
     * @param $checker mixed A class with a run() method
     * @param $zipFile string The path to the zip file with the test case
     * @param $arguments array Configuration of the plugin
     * @return \asm\plugin\PluginResponse
     */
    public static function runChecker( $checker, $zipFile, $arguments)
    {
        $testResult = $checker->run(array_merge([$zipFile], $arguments));
        return $testResult;
    }

    /**
     * @param $testCase PHPUnit_Framework_TestCase
     * @param $result \asm\plugin\PluginResponse
     * @param $fulfillment int
     * @param $details string
     */
    public static function assert($testCase, $filename, $result, $fulfillment = null, $details = "")
    {

        if ($fulfillment !== null)
        {
            $testCase->assertEquals(round($fulfillment), round($result->getFulfillment()), "Test case: " . $filename . "\nDetails: \n" . $result->getDetails() . "\nResults: ");
        }
        if ($details !== "")
        {
            $testCase->assertContains($details, $result->getDetails());
        }
    }
} 