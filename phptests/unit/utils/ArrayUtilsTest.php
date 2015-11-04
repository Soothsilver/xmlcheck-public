<?php
namespace asm\unittests;
use \asm\utils\ArrayUtils;

class ArrayUtilsTest extends \PHPUnit_Framework_TestCase
{

    public function testStripKeys()
    {
        $this->assertSame([1, 2], ArrayUtils::stripKeys(["one" => 1, "two" => 2]));
        $this->assertSame([1, [2], 3], ArrayUtils::stripKeys(["one" => 1, "two" => ["three" => 2], "four" => 3]));
        $this->assertNotEquals([["inner" => 1]], ArrayUtils::stripKeys([["inner" => 1]]));
        $this->assertSame([[1]], ArrayUtils::stripKeys([["inner" => 1]]));
        $this->setExpectedException('PHPUnit_Framework_Error');
        ArrayUtils::stripKeys("not an array");
    }

    public function testFilterByKeys()
    {
        $this->assertSame([1 => 'b', 'd' => 'e', 'h' => 'i'], ArrayUtils::filterByKeys(['a', 'b', 'c', 'd' => 'e', 'f' => 'g', 'h' => 'i'], array(0, 2, 'f'), false));
        $this->assertSame([0 => 'a', 1 => 'b', 3 => 'd'], ArrayUtils::filterByKeys(['a', 'b', 'c', 'd'], [0, 1, 3]));
    }

    public function testSortByKeys()
    {
        $this->assertSame([
            '2' => 'c',
            'f' => 'g',
            '1' => 'b',
            '0' => 'a',
            'd' => 'e',
            'h' => 'i'
        ], ArrayUtils::sortByKeys(
            array('a', 'b', 'c', 'd' => 'e', 'f' => 'g', 'h' => 'i'),
            array(2, 'f', 1)));
        $this->assertSame(['2'=>2,'1'=>1, '0'=>0], ArrayUtils::sortByKeys([0,1,2], [2,1]));
        $this->assertNotSame('2', array_keys(ArrayUtils::sortByKeys([0,1,2], [2,1]))[0] );
        $this->assertSame(2, array_keys(ArrayUtils::sortByKeys([0,1,2], [2,1]))[0] );
    }

    public function testMap()
    {
        $this->assertSame(['foofoofoo', 'bar'=>'bazbazbaz'], ArrayUtils::map('str_repeat', array('foo', 'bar' => 'baz'), 3));
        $this->assertSame(['1','2'], ArrayUtils::map(function ($number) { return $number . ""; }, [1,2]));

    }


}
 