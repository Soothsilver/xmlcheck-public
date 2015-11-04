<?php
namespace asm\unittests;
use asm\utils\Filesystem;
use XmlSchemaChecker;

require_once __DIR__ . "/checkerRunner.php";
require_once CheckerRunner::$xmlCheckRoot . '/files/plugins/XML XMLSchema/XmlSchemaChecker.php';

class XmlSchemaTest extends \PHPUnit_Framework_TestCase {
    public function testSimonRozsival()
    {
        $this->runSchemaTest('SimonRozsival.zip', 75, "defined mandatory attributes");
    }


    private function runSchemaTest($zipFile, $fulfillment = null, $details = "")
    {
        $result = CheckerRunner::runChecker(new XmlSchemaChecker(), Filesystem::combinePaths(CheckerRunner::$testCasesRoot, "SCHEMA",  $zipFile), []);
        CheckerRunner::assert($this, $zipFile, $result, $fulfillment, $details);
    }

    public function testAssert()
    {
        $this->runSchemaTest('AlexanderMansurov.zip', 50);
    }

    public function testAlexanderMansurovNoAssert()
    {
        $this->runSchemaTest('AlexanderNoAssert.zip', 75);
    }

    public function testWarplanOne()
    {
        $this->runSchemaTest('warplan_one.zip', 75);
    }

    public function testOptionalAttribute()
    {
        $this->runSchemaTest('warplan_withoptional.zip', 75);
    }

    public function testNoRefer()
    {
        $this->runSchemaTest('warplan_norefer.zip', 75);
    }

    public function testNoSchema()
    {
        $this->runSchemaTest('warplan_noschema.zip', 75);
    }

    public function testPetrHudecek()
    {
        $this->runSchemaTest('warplan.zip', 100);
    }

    public function testWarplanNamespace()
    {
        $this->runSchemaTest('warplan_namespace.zip', 100);
    }

    public function testDifferentNames()
    {
        $this->runSchemaTest('diff_names_ok.zip', 100);
    }

    public function testBadReferral()
    {
        $this->runSchemaTest('diff_names.zip', 75);
    }

    public function testschemaInstance()
    {
        $this->runSchemaTest('schemaInstance.zip', 100);
    }

    public function testschemaInstanceIncorrect()
    {
        $this->runSchemaTest('schemaInstance_wrong.zip', null, "The schemaLocation's attribute value's second part is not identical to the XSD filename found.");
    }



}