<?php
namespace asm\unittests;
use asm\utils\Filesystem;
use XpathChecker;

require_once __DIR__ . "/checkerRunner.php";
require_once CheckerRunner::$xmlCheckRoot . '/files/plugins/XML XPath/XpathChecker.php';

class XPathTest extends \PHPUnit_Framework_TestCase {
    public function testMinimalExample()
    {
        $this->runXPathTest('minimalExample.zip', 100);
    }

    private function runXPathTest($zipFile, $fulfillment = null, $details = "")
    {
        $result = CheckerRunner::runChecker(new XpathChecker(), Filesystem::combinePaths(CheckerRunner::$testCasesRoot, "XPATH",  $zipFile), [5]);
        CheckerRunner::assert($this, $zipFile, $result, $fulfillment, $details);
    }

    public function testInvalidComments()
    {
        $this->runXPathTest('minimalExampleBadComments.zip', 33);
    }

    public function testMissingDescendantNonexistenceTests()
    {
        $this->runXPathTest('minimalExampleNoDescendantNonexistence.zip', 67);
    }

    public function testPositionIsGivenUsingAbbreviation()
    {
        $this->runXPathTest('minimalExampleAbbreviatedPosition.zip', 100);
    }

    public function testThereIsWhitespaceInPositionPredicate()
    {
        $this->runXPathTest('minimalExampleWithPositionSpace.zip', 100);
    }

    public function testDTDsupplied()
    {
        $this->runXPathTest('warplanXpathDtd.zip', 100);
    }

    public function testDescendantTestHasItsOwnPredicate()
    {
        $this->runXPathTest('minimalExampleDescendantWithPredicate.zip', 100);
    }

    public function testRightsidePosition()
    {
        $this->runXPathTest('minimalExampleRightSidePosition.zip', 100);
    }

    public function testPavelBrozek()
    {
        $this->runXPathTest('brozek.zip', 100);
    }

    public function testJiriSvancara()
    {
        $this->runXPathTest('jiriSvancara.zip', 100);
    }


}