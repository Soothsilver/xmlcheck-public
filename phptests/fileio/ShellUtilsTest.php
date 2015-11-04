<?php
namespace asm\unittests;


use asm\utils\ShellUtils;

class ShellUtilsTest extends \PHPUnit_Framework_TestCase {


    public function testPhpExecInBackground()
    {
        if (file_exists('created.txt'))
        {
            unlink('created.txt');
        }
        ShellUtils::phpExecInBackground('php', 'file_put_contents("created.txt", "something");');
        sleep(1);
        $this->assertFileExists('created.txt');
        $this->assertSame('something', file_get_contents('created.txt'));
    }

    public function testQuotePhpArguments()
    {
        $this->assertSame("'hello', 'hi'", ShellUtils::quotePhpArguments(['hello', 'hi']));
        $this->assertSame("'hello', array(0 => 'hi', 1 => 'hi2')", ShellUtils::quotePhpArguments(['hello', ['hi', 'hi2']]));
        $this->assertSame("546", ShellUtils::quotePhpArgument(546));
        $this->assertSame("true", ShellUtils::quotePhpArgument(true));
        $this->assertSame("false", ShellUtils::quotePhpArgument(false));
        $this->assertSame("54.23", ShellUtils::quotePhpArgument(54.23));
        $this->assertSame("null", ShellUtils::quotePhpArgument(null));
    }

}
 