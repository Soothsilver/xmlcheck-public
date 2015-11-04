<?php
namespace asm\unittests;
use \asm\utils\Compression;

class CompressionTest extends \PHPUnit_Framework_TestCase {

    protected function setUp()
    {
        file_put_contents('hello.txt', 'hello');
        mkdir('hiDir');
        file_put_contents('hiDir/hi.txt', 'hi');
    }
    protected function tearDown()
    {
        unlink('hello.txt');
        unlink('hiDir/hi.txt');
        rmdir('hiDir');
    }
    public function testZip()
    {
        mkdir('extracted');
        $this->assertTrue(Compression::zip('hello.txt', 'hello.zip'));
        $this->assertTrue(file_exists('hello.zip'));
        $this->assertFalse(file_exists('extracted/hello.txt'));
        $this->assertTrue(Compression::unzip('hello.zip', 'extracted'));
        $this->assertTrue(file_exists('extracted/hello.txt'));
        unlink('hello.zip');
        unlink('extracted/hello.txt');
        rmdir('extracted');
    }
    public function testZipSingleFolder()
    {
        mkdir('extracted');
        $this->assertTrue(Compression::zip('hiDir', 'hi.zip'));
        $this->assertTrue(file_exists('hi.zip'));
        $this->assertFalse(file_exists('extracted/hi.txt'));
        $this->assertFalse(file_exists('extracted/hiDir'));
        $this->assertTrue(Compression::unzip('hi.zip', 'extracted'));
        $this->assertTrue(file_exists('extracted/hi.txt'));
        $this->assertFalse(file_exists('extracted/hiDir/hi.txt'));
        unlink('hi.zip');
        unlink('extracted/hi.txt');
        rmdir('extracted');
    }
}
 