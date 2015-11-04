<?php
namespace asm\unittests;
/*
 *
 * File permissions are not tested, because they do not work on Windows.
 *
 *
 *
 */
use asm\utils\Filesystem;


class FilesystemTest extends \PHPUnit_Framework_TestCase {
    const TEST_DIRECTORY = '__filesystemTest';


    public function testCombinePaths()
    {
        $this->assertEquals('abc/def', Filesystem::combinePaths('abc', 'def'));
        $this->assertEquals('abc/def', Filesystem::combinePaths('abc/', '/def/'));
        $this->assertEquals('', Filesystem::combinePaths('', '', ''));
        $this->assertEquals('/', Filesystem::combinePaths('','/'));
        $this->assertEquals('/a', Filesystem::combinePaths('/', '/a'));
        $this->assertEquals('/abc/def', Filesystem::combinePaths('/abc', 'def'));
        $this->assertEquals('foo.jpg', Filesystem::combinePaths('', 'foo.jpg'));
        $this->assertEquals('dir/0/a.jpg', Filesystem::combinePaths('dir', 0, 'a.jpg'));
        $this->assertEquals('C:/long/path/shortfile', Filesystem::combinePaths('C:\\long\\path\\', '/shortfile'));
    }

    public function testCopyIntoDirectory()
    {
        // Copy directory
        $whereTo = "whereTo/inside/";
        $this->assertFalse(is_dir($whereTo));
        $this->assertTrue(Filesystem::copyIntoDirectory(self::TEST_DIRECTORY, $whereTo));
        $this->assertTrue(is_dir($whereTo));
        $this->assertTrue(is_file($whereTo . "a.txt"));
        $this->assertTrue(is_file($whereTo . "b.txt"));
        Filesystem::removeDir($whereTo);
        $this->assertFalse(is_dir($whereTo));

        // Copy into file fails
        touch("whereToFile");
        $this->assertTrue(is_file("whereToFile"));
        $this->assertFalse(Filesystem::copyIntoDirectory(self::TEST_DIRECTORY . "/a.txt", "whereToFile"));
        $this->assertFalse(Filesystem::copyIntoDirectory(self::TEST_DIRECTORY , "whereToFile"));
        $this->assertTrue(is_file('whereToFile'));
        unlink('whereToFile');

        // Copy file into directory
        $this->assertFalse(is_dir($whereTo));
        $this->assertTrue(Filesystem::copyIntoDirectory(self::TEST_DIRECTORY . "/a.txt", Filesystem::combinePaths($whereTo)));
        $this->assertTrue(is_dir($whereTo));
        $this->assertTrue(is_file($whereTo . "a.txt"));
        $this->assertFalse(is_file($whereTo . "b.txt"));
        Filesystem::removeDir($whereTo);
        $this->assertFalse(is_dir($whereTo));
    }

    public function testRealPath()
    {
        $this->assertNotFalse(Filesystem::realPath(self::TEST_DIRECTORY));
        $this->assertNotFalse(Filesystem::realPath(self::TEST_DIRECTORY . "/a.txt"));
        $this->assertNotFalse(Filesystem::realPath(self::TEST_DIRECTORY . "/nonexistent.txt"));
        $this->assertFalse(Filesystem::realPath(self::TEST_DIRECTORY . "/nonexistent/nonexistent.txt"));
        $this->assertSame(Filesystem::combinePaths(realpath('.'), 'text'), Filesystem::realPath('text'));
    }

    public function testCreateDir()
    {
        $this->assertFalse(Filesystem::createDir(self::TEST_DIRECTORY . "/a.txt"));
        $this->assertTrue(Filesystem::createDir(self::TEST_DIRECTORY . "/a/b/c"));
        $this->assertTrue(is_dir(self::TEST_DIRECTORY . "/a/b"));
        $this->assertTrue(is_dir(self::TEST_DIRECTORY . "/a"));
        $this->assertTrue(is_dir(self::TEST_DIRECTORY . "/a/b/c"));
        Filesystem::removeDir(self::TEST_DIRECTORY ."/a");
        $this->assertFalse(file_exists(self::TEST_DIRECTORY . "/a"));
    }

    
    public function testStringToFile()
    {
        $this->assertTrue(Filesystem::stringToFile('a/test.txt', 'test content', 0666));
        $this->assertSame('test content', file_get_contents('a/test.txt'));
        $this->assertTrue(is_dir('a'));
        Filesystem::removeDir('a');
    }

    public function testRemoveDir()
    {
        mkdir('a/b/c/d/e', 0777, true);
        $this->assertTrue(is_dir('a/b/c'));
        $this->assertTrue(Filesystem::removeDir('a'));
        $this->assertFalse(is_dir('a/b/c'));
        $this->assertFalse(is_dir('a'));
        touch ('kaktus');
        $this->assertTrue(Filesystem::removeDir('kaktus'));
        $this->assertFalse(file_exists('kaktus'));
    }


    public function testRemoveFile()
    {
        touch ('kaktus');
        $this->assertTrue(Filesystem::removeFile('kaktus'));
        $this->assertFalse(file_exists('kaktus'));
    }

    public function testTempDir()
    {
        $folder = Filesystem::tempDir(null, "hello");
        $this->assertStringStartsWith('hello', basename($folder));
        $this->assertTrue(is_dir($folder));
        $this->assertEquals(0, count(array_diff(scandir($folder), ['.','..'])));
        $this->assertTrue(Filesystem::removeDir($folder));
        $this->assertFalse(is_dir($folder));
    }


    protected function setUp()
    {
        mkdir(self::TEST_DIRECTORY);
        touch(self::TEST_DIRECTORY . "/a.txt");
        touch(self::TEST_DIRECTORY . "/b.txt");
    }
    protected function tearDown()
    {
        unlink(self::TEST_DIRECTORY . "/a.txt");
        unlink(self::TEST_DIRECTORY . "/b.txt");
        rmdir(self::TEST_DIRECTORY);
    }

    public function testGetFiles()
    {
        $this->assertEquals(['a.txt', 'b.txt'],
            Filesystem::getFiles(self::TEST_DIRECTORY));
    }

}
