<?php
namespace asm\unittests;
use \asm\utils\Logger;

class LoggerTest extends \PHPUnit_Framework_TestCase {

    public function testLogger()
    {
        if (file_exists('log'))
        {
            \asm\utils\Filesystem::removeDir('log');
        }
        mkdir("log");

        $logger = Logger::create("log")
            ->setDatetimeFormat("d.M.Y H:i")
            ->setEntrySeparator("\nENTRY\n")
            ->setLineSeparator("\n")
            ->setPrefix("lg")
            ->setSuffix("suffix.log");

        $logger->setHeader("HEADER");
        $logger->flush();
        $this->assertFalse(file_exists('log/lg0suffix.log'));
        $logger->log("Something occurred.");
        $this->assertFalse(file_exists('log/lg0suffix.log'));
        $logger->log("Something else occurred.");
        $logger->flush();
        $this->assertTrue(file_exists('log/lg0suffix.log'));
        $contents = file_get_contents('log/lg0suffix.log');
        $this->assertStringStartsWith("\nENTRY\n", $contents);
        $this->assertStringEndsWith(" HEADER\nSomething occurred.\nSomething else occurred.", $contents);
        \asm\utils\Filesystem::removeDir("log");
    }

    public function testRotation()
    {
        if (file_exists('log'))
        {
            rmdir('log');
        }
        mkdir("log");

        $logger = Logger::create("log")
                        ->setDatetimeFormat("")
                        ->setEntrySeparator("")
                        ->setLineSeparator("")
                        ->setPrefix("l")
                        ->setSuffix("")
                        ->setMaxFileCount(4)
                        ->setMaxFileSize(10)
                        ->setHeader("");
        // Header is therefore one space that separates the (empty) date from the (empty) header text
        $logger->log("A23456789");
        $logger->flush();
        $logger->log("B23456789");
        $logger->flush();
        $logger->log("C23456789");
        $logger->flush();
        $logger->log("D23456789");
        $logger->flush();
        $logger->log("E23456789");
        $logger->flush();
        $logger->log("F23456789");
        $logger->flush();
        $logger->log("G23456789");
        $logger->flush();
        // We have filled 7 files, and maximum file count is 4.
        // It should have gone:
        // A l0
        // B l0, l1
        // C l0, l1, l2
        // D l0, l1, l2, l3
        // E l1, l2, l3, l4
        // F l0, l2, l3, l4
        // G l0, l1, l3, l4, where l1 is newest and l3 oldest


        $this->assertFileExists('log/l0');
        $this->assertFileExists('log/l1');
        $this->assertFileNotExists('log/l2');
        $this->assertFileExists('log/l3');
        $this->assertFileExists('log/l4');
        $this->assertFileNotExists('log/l5');
        $this->assertFileNotExists('log/l6');
        $this->assertFileNotExists('log/l7');
        $this->assertSame(' D23456789', file_get_contents('log/l3'));
        $this->assertSame(' E23456789', file_get_contents('log/l4'));
        $this->assertSame(' F23456789', file_get_contents('log/l0'));
        $this->assertSame(' G23456789', file_get_contents('log/l1'));

        \asm\utils\Filesystem::removeDir("log");
    }

}
 