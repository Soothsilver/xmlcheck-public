<?php
namespace asm\unittests;
use \asm\utils\Filter;

class FilterTest extends \PHPUnit_Framework_TestCase {

    public function testIsNonNegativeInt()
    {
        $this->assertTrue(Filter::isNonNegativeInt("10"));
        $this->assertTrue(Filter::isNonNegativeInt("1"));
        $this->assertTrue(Filter::isNonNegativeInt("0"));
        $this->assertFalse(Filter::isNonNegativeInt("-5"));
        $this->assertFalse(Filter::isNonNegativeInt("text"));
    }
    public function testHasLength()
    {
        $this->assertTrue(Filter::hasLength("neco", ['min_length' => 2, 'max_length' => 6]));
        $this->assertTrue(Filter::hasLength("12", ['min_length' => 2, 'max_length' => 6]));
        $this->assertTrue(Filter::hasLength("123456", ['min_length' => 2, 'max_length' => 6]));
        $this->assertFalse(Filter::hasLength("m", ['min_length' => 2, 'max_length' => 6]));
        $this->assertFalse(Filter::hasLength("1234567", ['min_length' => 2, 'max_length' => 6]));
    }

    public function testIsAlphaNumeric()
    {
        $this->assertTrue(Filter::isAlphaNumeric('abcdef'));
        $this->assertTrue(Filter::isAlphaNumeric('545df14gd5fg14s2df'));
        $this->assertFalse(Filter::isAlphaNumeric('dfsg/dfgdfg'));
        $this->assertTrue(Filter::isAlphaNumeric(''));
        $this->assertFalse(Filter::isAlphaNumeric('Příšerněžluťoučkýkůňúpělďábelskéódy'));
        $this->assertFalse(Filter::isAlphaNumeric('ř'));
    }
    public function testIsBool(){
        $this->assertTrue(Filter::isTrue("true"));
        $this->assertTrue(Filter::isTrue("1"));
        $this->assertTrue(Filter::isTrue("on"));
        $this->assertFalse(Filter::isTrue("false"));
        $this->assertFalse(Filter::isTrue(""));
        $this->assertFalse(Filter::isTrue("dsfgdfg"));
    }
    public function testIsNotEmpty() {
        $this->assertTrue(Filter::isNotEmpty('f'));
        $this->assertFalse(Filter::isNotEmpty(''));
        $this->assertFalse(Filter::isNotEmpty(null));
        $this->assertFalse(Filter::isNotEmpty(false));
    }
    public  function testIsName() {
        $this->assertTrue(Filter::isName("Petr Hudeček"));
        $this->assertTrue(Filter::isName("Irena Holubová"));
        $this->assertTrue(Filter::isName("ab"));
        $this->assertTrue(Filter::isName("Příšerně žlouťoučký kůň úpěl ďábelské ódy"));
        $this->assertTrue(Filter::isName("žščřďťňáéíóúůýěŽŠČŘĎŤŇÁÉÍÓÚÝ abcdefghijklmnopqrstvuwxyz"));
        $this->assertTrue(Filter::isName("Petr2"));
        $this->assertFalse(Filter::isName("Někdo \"přezdívka\" příjmení"));
        $this->assertFalse(Filter::isName(""));
        $this->assertFalse(Filter::isName(null));
    }
    public function testIsEmail()
    {
        $this->assertTrue(Filter::isEmail('petr@atlas.cz'));
        $this->assertTrue(Filter::isEmail('email@domain.com'));
        $this->assertTrue(Filter::isEmail('firstname.lastname@domain.com'));
        $this->assertTrue(Filter::isEmail('email@subdomain.domain.com'));
        $this->assertTrue(Filter::isEmail('firstname+lastname@domain.com'));
        //RFC ok, but PHP fails. $this->assertTrue(Filter::isEmail('email@123.123.123.123'));
        //RFC ok, but PHP fails. $this->assertTrue(Filter::isEmail('email@[123.123.123.123]'));
        $this->assertTrue(Filter::isEmail('"email"@domain.com'));
        $this->assertTrue(Filter::isEmail('1234567890@domain.com'));
        $this->assertTrue(Filter::isEmail('email@domain-one.com'));
        $this->assertTrue(Filter::isEmail('_______@domain.com'));
        $this->assertTrue(Filter::isEmail('email@domain.name'));
        $this->assertTrue(Filter::isEmail('email@domain.co.jp'));
        $this->assertTrue(Filter::isEmail('firstname-lastname@domain.com'));
        $this->assertFalse(Filter::isEmail('plainaddress'));
        $this->assertFalse(Filter::isEmail('#@%^%#$@#$@#.com'));
        $this->assertFalse(Filter::isEmail('@domain.com'));
        $this->assertFalse(Filter::isEmail('Joe Smith <email@domain.com>'));
        $this->assertFalse(Filter::isEmail('email.domain.com'));
        $this->assertFalse(Filter::isEmail('email@domain@domain.com'));
        $this->assertFalse(Filter::isEmail('.email@domain.com'));
        $this->assertFalse(Filter::isEmail('email.@domain.com'));
        $this->assertFalse(Filter::isEmail('email..email@domain.com'));
        $this->assertFalse(Filter::isEmail('あいうえお@domain.com'));
        $this->assertFalse(Filter::isEmail('email@domain.com (Joe Smith)'));
        $this->assertFalse(Filter::isEmail('email@domain'));
        $this->assertFalse(Filter::isEmail('email@-domain.com'));
        //RFC incorrect, but PHP accepts. $this->assertFalse(Filter::isEmail('email@domain.web'));
        $this->assertFalse(Filter::isEmail('email@111.222.333.44444'));
        $this->assertFalse(Filter::isEmail('email@domain..com'));
    }
    public function testIsIndex()
    {
        $this->assertTrue(Filter::isIndex("10"));
        $this->assertTrue(Filter::isIndex("1"));
        $this->assertTrue(Filter::isIndex("0"));
        $this->assertFalse(Filter::isIndex("-5"));
        $this->assertFalse(Filter::isIndex("text"));
    }

    public function testIsEnum() {
        $this->assertTrue(Filter::isEnum("a", ["a",'b']));
        $this->assertFalse(Filter::isEnum("c", ["a",'b']));
    }
    public function testIsDate()
    {
        $this->assertTrue(Filter::isDate("2010-01-01"));
        $this->assertFalse(Filter::isDate("sdfgsdfg"));
        $this->assertFalse(Filter::isDate("2010-01-01 10:10"));
        $this->assertFalse(Filter::isDate("2010-02-30"));
    }

}
 