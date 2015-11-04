<?php
namespace asm\unittests;

// NOTE: we cannot test state before the calling of :init() because due to its singleton pattern,
// the data in Config is kept between unit test methods. We are sure, however, that calling init()
// destroys the old state, so unit testing can be reasonably performed.

class ConfigTest extends \PHPUnit_Framework_TestCase {

    public function testLoadSuccessful()
    {
        \asm\core\Config::init("myconfig.ini");
    }

    public function testLoadNormally()
    {
        \asm\core\Config::init("myconfig.ini");
        $this->assertSame("valueContents", \asm\core\Config::get("sectionName", "propertyName"));
    }

    public function testBadly()
    {
        \asm\core\Config::init("myconfig.ini");
        $this->assertNull(\asm\core\Config::get("sectionName", "thisPropertyDoesNotExist"));
        $this->assertNull(\asm\core\Config::get("thisSectionDoesNotExist", "thisPropertyDoesNotExist"));
        $this->assertNull(\asm\core\Config::get("thisSectionDoesNotExist"));

    }

    public function testRealConfig()
    {
        \asm\core\Config::init('myconfig.ini');
        $webRoot = \asm\core\Config::get('roots', 'web'); // hack
        \asm\core\Config::init(\asm\utils\Filesystem::combinePaths($webRoot, "core/config.ini"),
            \asm\utils\Filesystem::combinePaths($webRoot, "core/internal.ini"));
    }

    protected function setUp()
    {
        $iniFile = <<<HEREDOC
[sectionName]
propertyName = "valueContents" ;comment
;anotherComment
[paths]
coolfile = "index.php"
[folderStructure]
roots.web[] = "paths.coolfile"
HEREDOC;
        $iniFile2 = <<<HEREDOC
[sectionName]
propertyName = "valueContents" ;comment
;anotherComment
[paths]
coolfile = "index.php"
nonexistingfile = "nonexisting.php"
[folderStructure]
roots.web[] = "paths.coolfile"
roots.web[] = "paths.nonexistingfile"
HEREDOC;
        file_put_contents("myconfig.ini", $iniFile);
        file_put_contents("myconfig2.ini", $iniFile2);
    }

    protected function tearDown()
    {
        \asm\utils\Filesystem::removeFile("myconfig.ini");
        \asm\utils\Filesystem::removeFile("myconfig2.ini");
    }

    public function testNonExistingThrowsException()
    {
        $this->setExpectedException("\Exception");
        \asm\core\Config::init("myconfig2.ini");
    }

}
