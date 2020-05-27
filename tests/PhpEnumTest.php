<?php
/**
 * User: evaisse
 * Date: 27/05/2020
 * Time: 14:43
 */

namespace evaisse\SimplePhpEnum\Tests;

use evaisse\SimplePhpEnum\PhpEnum;
use PHPUnit\Framework\TestCase;

/**
 * Class PhpEnumTest
 * @package PhpSimpleEnum\Tests\PhpEnum
 */
class PhpEnumTest extends TestCase
{

    /**
     *
     */
    public function testClassConstants()
    {
        $enum = PhpEnum::fromConstants('\evaisse\SimplePhpEnum\Tests\TestClass1::T_*');

        $this->assertCount(3, $enum->getAllowedValues());
        $this->assertTrue($enum->isAllowed(TestClass1::T_BAR));
        $this->assertTrue($enum->isAllowed(TestClass1::T_FOO_BAR));

        $this->assertTrue($enum->isAllowed(1));
        $this->assertFalse($enum->isAllowed("1"));
        $this->assertFalse($enum->isAllowed(22));

        $this->assertEquals('T_FOO_STRING', $enum->getKeyForValue(TestClass1::T_FOO_STRING));

        $enum1 = PhpEnum::fromConstants('\evaisse\SimplePhpEnum\Tests\TestClass1::T_*');
        $enum2 = PhpEnum::fromConstants('\evaisse\SimplePhpEnum\Tests\TestClass1::T_*');

        $this->assertEquals($enum->getAllowedValues(), $enum1->getAllowedValues(), 'ensure cache is equal');
        $this->assertEquals($enum->getAllowedValues(), $enum2->getAllowedValues(), 'ensure cache is equal');

        $enum = PhpEnum::fromConstants('PHP_INT_*');
        $this->assertCount(3, $enum->getAllowedValues());
        $this->assertTrue($enum->isAllowed(PHP_INT_SIZE));
        $this->assertFalse($enum->isAllowed(PHP_VERSION));
        $this->assertEquals('PHP_INT_SIZE', $enum->getKeyForValue(PHP_INT_SIZE));

        $val = $enum->validate(PHP_INT_SIZE);
        $this->assertEquals(PHP_INT_SIZE, $val);

        try {
            $enum->validate("aaaa");
            $this->assertTrue(false);
        } catch (\InvalidArgumentException $e) {
            $this->assertRegExp('/^invalid enum .*/i', $e->getMessage());
            $this->assertTrue(true);
        }

        $this->assertEquals($enum['PHP_INT_SIZE'], PHP_INT_SIZE, 'ensure array access work as wel');
        $this->assertNotTrue(array_key_exists('PHP_INT_SIZE', $enum));
        $this->assertTrue(empty($enum['NIMP']));
        $this->assertNotTrue(array_key_exists('NIMP', $enum));
    }

}
