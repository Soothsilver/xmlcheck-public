<?php
namespace asm\unittests;
use \asm\utils\ErrorHandler;

class ErrorHandlerTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException   \PHPUnit_Framework_Error_Notice
     */
    public function testExceptionNoRegistering()
    {
        $ar = [];
        $sth = $ar['notice error'];
    }
}
 