<?php

namespace evaisse\SimplePhpEnum;

/**
 * Class PhpSimpleEnum
 * @usage
 *
 *      $enum = \PhpSimpleEnum::fromConstants('\Symfony\Component\HttpFoundation\Request::METHOD_*');
 *      $this->assertCount(10, $enum->getAllowedValues());
 *      $this->assertTrue($enum->isAllowed(\Symfony\Component\HttpFoundation\Request::METHOD_GET));
 *      $this->assertFalse($enum->isAllowed('get'));
 *      $this->assertFalse($enum->isAllowed('AAA'));
 *      $this->assertEquals('METHOD_GET', $enum->getKeyForValue(\Symfony\Component\HttpFoundation\Request::METHOD_GET));
 *
 *      $enum = \PhpSimpleEnum::fromConstants('PHP_INT_*');
 *      $this->assertCount(3, $enum->getAllowedValues());
 *      $this->assertTrue($enum->isAllowed(PHP_INT_SIZE));
 *      $this->assertFalse($enum->isAllowed(PHP_VERSION));
 *      $this->assertEquals('PHP_INT_SIZE', $enum->getKeyForValue(PHP_INT_SIZE));
 */
class PhpEnum implements \JsonSerializable, \ArrayAccess
{

    /**
     * @var array the raw list for this enum, [foo => 1, bar => 12]
     */
    protected $enum = [];

    /**
     * @param string $constantPattern a given constant pattern to extract a list of key => values, i.e. \Symfony\Component\HttpFoundation\Request::METHOD_*
     * @return self|null
     * @example PHP_INT_*
     * @example \Symfony\Component\HttpFoundation\Request::METHOD_* => ['METHOD_GET' => 'GET, 'METHOD_POST' => 'POST' ...]
     */
    public static function fromConstants($constantPattern)
    {
        static $cache = [];

        if (!array_key_exists(strtolower($constantPattern), $cache)) {

            if (strpos($constantPattern, '::')) {
                list($cls, $constantPattern) = explode('::', $constantPattern);
                try {
                    $refl = new \ReflectionClass($cls);
                    $constants = $refl->getConstants();
                } catch (\ReflectionException $e) {
                    return null;
                }
            } else {
                $constants = get_defined_constants();
            }

            $values = [];

            foreach ($constants as $name => $val) {
                if (fnmatch($constantPattern, $name)) {
                    $values[$name] = $val;
                }
            }

            /*
            	Ensure there is no duplicate values in enums
             */
            if (count(array_unique($values)) !== count($values)) {
                throw new \InvalidArgumentException('Invalid duplicate values for enum '.$constantPattern);
            }

            $cache[strtolower($constantPattern)] = $values;

        } else {
            $values = $cache[strtolower($constantPattern)];
        }

        return new static($values);
    }

    /**
     * PhpSimpleEnum constructor.
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        // do not take multiple values, but keep keys
        $vals = array_unique($values);

        foreach ($vals as $val) {
            foreach ($values as $k => $v) {
                if (!is_scalar($v)) {
                    throw new \LogicException('PhpSimpleEnum cannot handle non-scalar values');
                }
                if ($v === $val) {
                    $this->enum[$k] = $val;
                }
            }
        }
    }

    /**
     * @return array return the whole enum with [label => value]
     */
    public function getHash()
    {
        return $this->enum;
    }

    /**
     * @return array a list allowed values for this enum
     */
    public function getAllowedValues()
    {
        return array_values($this->enum);
    }


    /**
     * @param int|float|string|boolean $value
     * @return int|float|string|boolean given value if allowed by the enum
     * @throws \InvalidArgumentException
     */
    public function validate($value)
    {
        if (!$this->isAllowed($value)) {
            throw new \InvalidArgumentException(
                'Invalid Enum value '.$value
                .' for enum : '.$value
            );
        }

        return $value;
    }

    /**
     * @param int|bool|float|string $value a given scalar value to test against enum list
     * @return bool true if value is allowed by enum, false otherwise
     */
    public function isAllowed($value)
    {
        return in_array($value, $this->getAllowedValues(), true);
    }

    /**
     * @param int|bool|float|string $value
     * @return null|string null if value does not exist, string or otherwise
     */
    public function getKeyForValue($value)
    {
        foreach ($this->enum as $k => $v) {
            if ($value === $v) {
                return $k;
            }
        }

        return null;
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return array_values($this->enum);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->enum);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->enum[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        throw new \InvalidArgumentException('PhpSimpleEnum is frozen');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        throw new \InvalidArgumentException('PhpSimpleEnum is frozen');
    }
    
}
