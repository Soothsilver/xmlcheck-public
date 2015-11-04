<?php
namespace asm\unittests;
use asm\utils\Filesystem;
use Dtd2014Checker;

require_once __DIR__ . "/checkerRunner.php";
require_once CheckerRunner::$xmlCheckRoot . '/files/plugins/DTD2014/Dtd2014Checker.php';

class DtdTest extends \PHPUnit_Framework_TestCase {

    public function testChinese()
    {
        $this->runDtdTest("chinese.zip", null,"UTF-16 but has UTF-8" );
    }
    public function testMacOsX()
    {
        $this->runDtdTest("macosx.zip", 100);
    }
    public function testNotMacOsX()
    {
        $this->runDtdTest("not_macosx.zip", 0, "Your submission must contain an XML file ending with '.xml'.");
    }



    private function runDtdTestFull($zipFile, $fulfillment = null, $details = "")
    {
        // This test requires depth 5 and maximum fan-out 10.
        $result = CheckerRunner::runChecker(new Dtd2014Checker(), Filesystem::combinePaths(CheckerRunner::$testCasesRoot, "DTD",  $zipFile), [5, 10]);
        CheckerRunner::assert($this, $zipFile, $result, $fulfillment, $details);
    }
    private function runDtdTest($zipFile, $fulfillment = null, $details = "")
    {
        $result = CheckerRunner::runChecker(new Dtd2014Checker(), Filesystem::combinePaths(CheckerRunner::$testCasesRoot, "DTD",  $zipFile), [0, 0]);
        CheckerRunner::assert($this, $zipFile, $result, $fulfillment, $details);
    }

    public function testInternalSubset()
    {
        $this->runDtdTest("internalSubset.zip", 100);
    }


    public function testNotationWithPercentSign()
    {
        // Hacked success: bypassing PHP bug https://bugs.php.net/bug.php?id=67012
        $this->runDtdTestFull("notationWithPercentSign.zip", 14, "Please upload a document without using the percent sign");
    }

    public function testNotationWithBadQuoting()
    {
        // Yes, this is the bad quoted file. The filename is simply badly chosen.
        $this->runDtdTestFull("notationWithPercentSign_correct.zip", 0, "In a general entity declaration, the keyword NDATA must be followed by a Name only. It is followed by something else, however.");
    }

    public function testEmpty()
    {
        $this->runDtdTestFull("empty.zip", 0, "ZIP extraction failed");
    }

    public function testNotAZipFile()
    {
        $this->runDtdTestFull("notZip.txt.zip", 0, "ZIP extraction failed");
    }

    public function testStandaloneButWithExternalEntityDeclared()
    {
        $this->runDtdTestFull("ImrichKuklis.zip", 29, "document marked standalone but requires external subset");
    }
    public function testXmlFileNotZip()
    {
        $this->runDtdTestFull("data.xml", 0, "ZIP extraction failed");
    }
    public function testVeronikaMaurerova()
    {
        $this->runDtdTestFull("VeronikaLastFixed.zip", 71, "Documents contain 5 maximum fan-out of (minimum of 10 required)");
    }

    public function testInternalSubsetConditionalSection()
    {
        $this->runDtdTest("internalSubsetConditionalSection.zip", 14);
    }
    public function testDiacritics()
    {
        $this->runDtdTest("diakritika.zip", 100);
    }

    public function testUTF16LittleEndianWithBom()
    {
        $this->runDtdTest("utf16lebom_twice.zip", 100);
    }

    public function testUTF16BigEndianWithBom()
    {
        $this->runDtdTest("utf16bebom_twice.zip", 100);
    }

    public function testUtf8DeclaredButReallyUTF16LittleEndian()
    {
        $this->runDtdTest("utf8_but_utf16le.zip", 100);
    }

    public function testUtf8Minimal()
    {
        $this->runDtdTest("utf8.zip", 100);
    }

    public function testReferToNonexistentDTD()
    {
        $this->runDtdTest("norefer.zip", 29);
    }

    public function testMissingEncoding()
    {
        $this->runDtdTest("okruhlica2.zip", 29);
    }

    public function testSpaceNeededHere()
    {
        $this->runDtdTest("okruhlica.zip", 29, "common error");
    }

    public function testIncorrectInnerComment()
    {
        $this->runDtdTest("badinnercomment.zip", 0);
    }

    public function testNonstandardFileNames()
    {
        $this->runDtdTest("strangenames.zip", 57);
    }

    public function testNotations()
    {
        $this->runDtdTest("notation.zip", 57);
    }
    public function testMinimalIncomplete()
    {
        $this->runDtdTest("minimalValidNotComplete.zip", 57);
    }
    public function testPetrHudecek()
    {
        $this->runDtdTestFull("XmlDtd_correct_warplan.zip", 100);
    }
    public function testVeronikaMaurerova2()
    {
        $this->runDtdTestFull("Veronika Maurerova.zip", 86,"Documents contain 5 maximum fan-out of (minimum of 10 required)");

    }
    public function testImrichKuklis()
    {
        $this->runDtdTestFull("ImrichKuklisFixed.zip", 86);
    }
    public function testNoDtd()
    {
        $this->runDtdTest("nodtd.zip", 0);
    }
    public function testMultipleXmlFiles()
    {
        $this->runDtdTest("multiplexml.zip", 0);
    }



}