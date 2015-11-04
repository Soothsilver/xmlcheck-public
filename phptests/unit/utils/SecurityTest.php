<?php
namespace asm\unittests;


use asm\utils\Security;

class SecurityTest extends \PHPUnit_Framework_TestCase {


    public function testHash()
    {
        $this->assertEquals("5d41402abc4b2a76b9719d911017c592", Security::hash("hello", Security::HASHTYPE_MD5));
        $this->assertEquals("49f68a5c8493ec2c0bf489821c21fc3b", Security::hash("hi", Security::HASHTYPE_MD5));
        $this->assertStringStartsWith('$2a$08$', Security::hash("hello"));
        $this->assertStringStartsWith('$2a$08$', Security::hash("hello", Security::HASHTYPE_PHPASS));
    }

    public function testCheck()
    {
        $this->assertTrue(Security::check("hello","5d41402abc4b2a76b9719d911017c592", Security::HASHTYPE_MD5 ));
        $this->assertTrue(Security::check("hi","49f68a5c8493ec2c0bf489821c21fc3b", Security::HASHTYPE_MD5 ));
        $this->assertTrue(Security::check("hello",'$2a$08$uHDGnFAtkAbBdH/iRt.jQOViR6bd2g3wwn6IS7MyvlMHoMvvBXDyi', Security::HASHTYPE_PHPASS ));
        $this->assertFalse(Security::check("hello2", "5d41402abc4b2a76b9719d911017c592", Security::HASHTYPE_MD5));
        $this->assertFalse(Security::check("hello2", "hello2", Security::HASHTYPE_MD5));
        $this->assertFalse(Security::check("hello2", "5d41402abc4b2a76b9719d911017c592", Security::HASHTYPE_PHPASS));
    }

}
 