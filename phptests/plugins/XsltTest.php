<?php
namespace asm\unittests;

use asm\utils\Filesystem;
use XsltChecker;

require_once __DIR__ . "/checkerRunner.php";
require_once CheckerRunner::$xmlCheckRoot . '/files/plugins/XML XSLT/XsltChecker.php';

/**
 * Tests the XSLT plugin.
 */
class XsltTest extends \PHPUnit_Framework_TestCase {
    private function runXsltTest($zipFile, $templateCount, $fulfillment = null, $details = "")
    {
        $result = CheckerRunner::runChecker(new XsltChecker(), Filesystem::combinePaths(CheckerRunner::$testCasesRoot, "XSLT",  $zipFile), [$templateCount]);
        CheckerRunner::assert($this, $zipFile, $result, $fulfillment, $details);
    }

    public function testPetrHudecek()
    {
        $this->runXsltTest("warplan_xslt.zip", 5, 100);
    }

    public function testStrangeXPathExpression()
    {
        $this->runXsltTest('VojtechVondra.zip', 5, 100);
    }

    public function testXml11()
    {
        $this->runXsltTest('TomasHogenauer.zip', 5, null, "Unsupported version '1.1'");
    }

    public function testXslt20()
    {
        $this->runXsltTest('warplan_xslt_version2.zip', 5, 50, "only 1.0 features are supported");
    }

    public function testDifferentNamespaceName()
    {
        $this->runXsltTest('nero_namespace.zip', 5, 100);
    }

    public function testDifferentFileNames()
    {
        $this->runXsltTest('weirdnames.zip', 5, 100);
    }

    public function testTwoXmlFilesAndTwoXslFiles()
    {
        $this->runXsltTest('multi.zip', 5, 0, "There are two or more .xsl files in your submission.");
        $this->runXsltTest('multi.zip', 5, 0, "There are two or more .xml files in your submission.");
    }

}