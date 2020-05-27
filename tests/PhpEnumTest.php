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
        $this->assertNull($enum->getKeyForValue('TADA'));

        $enum1 = PhpEnum::fromConstants('\evaisse\SimplePhpEnum\Tests\TestClass1::T_*');
        $enum2 = PhpEnum::fromConstants('\evaisse\SimplePhpEnum\Tests\TestClass1::T_*');

        $this->assertEquals($enum->getAllowedValues(), $enum1->getAllowedValues(), 'ensure cache is equal');
        $this->assertEquals($enum->getAllowedValues(), $enum2->getAllowedValues(), 'ensure cache is equal');

        $enum = PhpEnum::fromConstants('PHP_INT_*');
        $this->assertCount(version_compare(PHP_VERSION, '7.0.0', '<') ? 2 : 3, $enum->getAllowedValues());
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
        $this->assertTrue(empty($enum['NIMP']));
    }



    /**
     * test how the enum should fail to assert there is a duplicate value in the enum
     */
    public function testDuplicateValues()
    {
        try {
            PhpEnum::fromConstants('\evaisse\SimplePhpEnum\Tests\TestClassDuplicate::BAD_*');
            $this->assertFalse(true, 'should throw');
        } catch (\Exception $e) {
            $this->assertInstanceOf(\InvalidArgumentException::class, $e);
        }
    }


    /**
     * test how the enum should fail to assert there is a duplicate value in the enum
     */
    public function testNotScalarTypes()
    {
        try {
            new PhpEnum([
                'FOO' => 'bar',
                'BAR'  => fopen(__FILE__, 'r')
            ]);
            $this->assertFalse(true, 'should throw');
        } catch (\Exception $e) {
            $this->assertInstanceOf(\LogicException::class, $e);
        }

        try {
            new PhpEnum([
                'FOO' => new \stdClass(),
            ]);
            $this->assertFalse(true, 'should throw');
        } catch (\Exception $e) {
            $this->assertInstanceOf(\LogicException::class, $e);
        }

        try {
            new PhpEnum([
                'FOO' => [],
            ]);
            $this->assertFalse(true, 'should throw');
        } catch (\Exception $e) {
            $this->assertInstanceOf(\LogicException::class, $e);
        }
    }

    /**
     * Test integrity of the stored hashMap
     */
    public function testHashIntegrity()
    {
        $enum = PhpEnum::fromConstants('PHP_INT_*');
        $hash = $enum->getHash();

        $this->assertNotEmpty($hash);

        $enum = new PhpEnum($hash);

        $this->assertEquals($hash, $enum->getHash());
        $this->assertEquals(json_encode(array_values($hash)), json_encode($enum));
    }

    public function testImmutableState()
    {
        $enum = PhpEnum::fromConstants('PHP_INT_*');

        try {
            $enum['foo'] = 2;
            $this->assertFalse(true, 'should throw');
        } catch (\Exception $e) {
            $this->assertInstanceOf(\LogicException::class, $e);
        }

        try {
            unset($enum['foo']);
            $this->assertFalse(true, 'should throw');
        } catch (\Exception $e) {
            $this->assertInstanceOf(\LogicException::class, $e);
        }
    }

}
