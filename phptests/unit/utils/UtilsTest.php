<?php
namespace asm\unittests;


class UtilsTest extends \PHPUnit_Framework_TestCase {

    public function testParseBool()
    {
        $this->assertTrue(\asm\utils\Utils::parseBool('true'));
        $this->assertTrue(\asm\utils\Utils::parseBool('yes'));
        $this->assertTrue(\asm\utils\Utils::parseBool('y'));

        $this->assertFalse(\asm\utils\Utils::parseBool('false'));
        $this->assertFalse(\asm\utils\Utils::parseBool('no'));
        $this->assertFalse(\asm\utils\Utils::parseBool('n'));
        $this->assertFalse(\asm\utils\Utils::parseBool('0'));
        $this->assertFalse(\asm\utils\Utils::parseBool(''));

        $this->assertNull(\asm\utils\Utils::parseBool('sthelse'));
    }

    public function testEndsWith()
    {
        $this->assertTrue(\asm\utils\Utils::endsWith("HELLO", "LLO"));
        $this->assertTrue(\asm\utils\Utils::endsWith("HELLO", ""));
        $this->assertTrue(\asm\utils\Utils::endsWith("HELLO", "LO"));
        $this->assertTrue(\asm\utils\Utils::endsWith("HELLO", "O"));
        $this->assertTrue(\asm\utils\Utils::endsWith("HELLO", "ELLO"));
        $this->assertTrue(\asm\utils\Utils::endsWith("HELLO", "HELLO"));
        $this->assertFalse(\asm\utils\Utils::endsWith("HELLO", "hello"));
        $this->assertFalse(\asm\utils\Utils::endsWith("HELLO", "lo"));
        $this->assertFalse(\asm\utils\Utils::endsWith("HELLO", "HESTR"));
        $this->assertFalse(\asm\utils\Utils::endsWith("HELLO", "H"));
        $this->assertFalse(\asm\utils\Utils::endsWith("HELLO", "ALLO"));
        $this->assertFalse(\asm\utils\Utils::endsWith("HELLO", "Some long string"));
    }

    public function testEndsWithCaseInsensitive()
    {
        $this->assertTrue(\asm\utils\Utils::endsWithIgnoreCase("HELLO", "LLO"));
        $this->assertTrue(\asm\utils\Utils::endsWithIgnoreCase("HELLO", ""));
        $this->assertTrue(\asm\utils\Utils::endsWithIgnoreCase("HELLO", "LO"));
        $this->assertTrue(\asm\utils\Utils::endsWithIgnoreCase("HELLO", "O"));
        $this->assertTrue(\asm\utils\Utils::endsWithIgnoreCase("HELLO", "ELLO"));
        $this->assertTrue(\asm\utils\Utils::endsWithIgnoreCase("HELLO", "HELLO"));
        $this->assertTrue(\asm\utils\Utils::endsWithIgnoreCase("HELLO", "hello"));
        $this->assertTrue(\asm\utils\Utils::endsWithIgnoreCase("HELLO", "lo"));
        $this->assertFalse(\asm\utils\Utils::endsWithIgnoreCase("HELLO", "HESTR"));
        $this->assertFalse(\asm\utils\Utils::endsWithIgnoreCase("HELLO", "H"));
        $this->assertFalse(\asm\utils\Utils::endsWithIgnoreCase("HELLO", "ALLO"));
        $this->assertFalse(\asm\utils\Utils::endsWithIgnoreCase("HELLO", "Some long string"));
    }


}
 