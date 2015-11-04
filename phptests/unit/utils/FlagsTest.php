<?php
namespace asm\unittests;
/**
 * Created by PhpStorm.
 * User: Petrik
 * Date: 1.10.2014
 * Time: 20:27
 */

use\asm\utils\Flags;

class FlagsTest extends \PHPUnit_Framework_TestCase {

    public function testMatch()
    {
        $set = 0b11111111;
        $this->assertTrue(Flags::match($set, 0b100));
        $this->assertTrue(Flags::match($set, 0b1000));
        $this->assertTrue(Flags::match($set, 0b10));
        $this->assertTrue(Flags::match($set, 156));
        $this->assertFalse(Flags::match($set, 300));
        $this->assertFalse(Flags::match($set, 257));
        $this->assertFalse(Flags::match($set, 4454));
        $this->assertTrue(Flags::match($set, 4454, 20));
        $this->assertFalse(Flags::match($set, 4454, 5414));
        $this->assertTrue(Flags::match($set, 4454, 8787, 20));
    }

    public function testMatchUpperLimit()
    {
        // All set (31 bits, more is not supported, because it turns to float, then.
        $set = 0b01111111111111111111111111111111;
        $this->assertTrue(Flags::match($set, $set));
        $this->assertTrue(Flags::match($set, 0b1011010001010110));
        $this->assertFalse(Flags::match($set, 0b1111111111111111111111111111111111111111111111111111111111111111));
    }

    public function testMatchNotAll()
    {
        $set = 0b1010;
        $this->assertTrue(Flags::match($set, 0b1000, 0b100));
        $this->assertTrue(Flags::match($set, 0b1000 |  0b10));
        $this->assertFalse(Flags::match($set, 0b1000 |  0b10 | 0b100));
    }


    public function testFlagsTooBig()
    {
        $this->setExpectedException('InvalidArgumentException');
        $flags = new Flags([1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32]);
    }

    public function testGetFlag()
    {
        $flags = new Flags(['first', 'second', 'third']);
        $this->assertEquals(0b1, $flags->getFlag('first'));
        $this->assertEquals(0b10, $flags->getFlag('second'));
        $this->assertEquals(0b100, $flags->getFlag('third'));
        $this->assertEquals(0, $flags->getFlag('nonexistent'));
    }

    public function testToArray()
    {
        $flags = new Flags(['first', 'second', 'third']);
        $array = $flags->toArray($flags->getFlag('first') | $flags->getFlag('third'));
        $this->assertCount(3, $array);
        $this->assertArrayHasKey('first', $array);
        $this->assertArrayHasKey('second', $array);
        $this->assertArrayHasKey('third', $array);
        $this->assertSame(true, $array['first']);
        $this->assertSame(false, $array['second']);
        $this->assertSame(true, $array['third']);
    }

    public function testToInteger()
    {
        $flags = new Flags(['first', 'second', 'third']);
        $array = $flags->toArray($flags->getFlag('first') | $flags->getFlag('third'));
        $integer = $flags->toInteger($array);
        $this->assertEquals(0b101, $integer);
    }


}
