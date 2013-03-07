<?php

namespace PHPPHP\Engine\Zval;

use PHPPHP\Engine\Zval;
use PHPPHP\Engine\Objects\ClassInstance;
use PHPPHP\Engine\ExecuteData;

class Value extends Zval {

    protected $value;
    protected $refcount = 1;
    protected $isRef = false;

    protected $dtorFunc;

    public function __construct($value, $dtorFunc = null) {
        $this->setValue($value);
        $this->dtorFunc = $dtorFunc;
    }

    public function __clone() {
        $this->refcount = 1;
        $this->isRef = false;
    }

    public function getRefcount() {
        return $this->refcount;
    }

    public function addRef() {
        $this->refcount++;
    }

    public function delRef() {
        $this->refcount--;
        if ($this->refcount <= 0 && $this->dtorFunc) {
            call_user_func($this->dtorFunc, $this);
        }
    }

    public function isRef() {
        return $this->isRef;
    }

    public function makeRef() {
        $this->isRef = true;
    }

    public function getZval() {
        return $this;
    }

    public function getValue() {
        return $this->value;
    }

    public function getIterator() {
        if (is_array($this->value)) {
            return new \ArrayIterator($this->value);;
        }
        throw new \LogicException('No Default Iterator Provided');
    }

    public function getType() {
        return gettype($this->value);
    }

    public function makePrintable() {
        $type = gettype($this->value);
        switch ($type) {
            case 'NULL':
                $ret = '';
                break;
            case 'array':
                $ret = 'array';
                break;
            case 'boolean':
                $ret = $this->value ? '1' : '';
                break;
            case 'double':
            case 'float':
            case 'integer':
            case 'long':
            case 'string':
                $ret = (string) $this->value;
                break;
            default:
                throw new \LogicException('Unknown Type: ' . $type);
        }
        return self::factory($ret);
    }

    public function castTo($type) {
        switch ($type) {
            case 'null':
                return static::factory(null);
            case 'array':
                return static::factory($this->toArray());
            case 'bool':
                return static::factory($this->toBool());
            case 'double':
                return static::factory($this->toDouble());
            case 'long':
                return static::factory($this->toLong());
            case 'string':
                return static::factory($this->toString());
            default:
                throw new \LogicException('Unknown Type: ' . $type);
        }
    }

    public function isArray() {
        return is_array($this->value);
    }

    public function isBool() {
        return is_bool($this->value);
    }
    public function isDouble() {
        return is_double($this->value);
    }

    public function isLong() {
        return is_int($this->value);
    }

    public function isNull() {
        return is_null($this->value);
    }

    public function isString() {
        return is_string($this->value);
    }

    public function isObject() {
        return $this->value instanceof ClassInstance;
    }

    public function toArray() {
        return (array) $this->value;
    }

    public function toBool() {
        return (bool) $this->value;
    }

    public function toDouble() {
        return (double) $this->value;
    }

    public function toLong() {
        return (int) $this->value;
    }

    public function toString() {
        if ($this->value instanceof \PHPPHP\Engine\Objects\ClassInstance) {
            debug_print_backtrace(false);
        }
        return (string) $this->value;
    }

    public function toObject(ExecuteData $data) {
        if ($this->isObject()) {
            return $this->value;
        } else {
            if ($this->isArray()) {
                $properties = $this->value;
            } else {
                $properties = array(
                    'scalar' => Zval::ptrFactory($this->value),
                );
            }
            $ce = $data->executor->getClassStore()->get('stdClass');
            return $ce->instantiate($data, $properties);
        }
    }

    public function setValue($value) {
        if ($value instanceof self) {
            $value = $value->value;
        } elseif ($value instanceof Zval) {
            $value = $value->getValue();
        }

        $this->value = $value;
    }

    public function &getArray() {
        if (is_array($this->value)) {
            return $this->value;
        }
        throw new \LogicException('Getting array on non-array');
    }

    public function isEqualTo(Zval $other) {
        $type = $this->getType();
        $otherType = $other->getType();
        if ('array' === $type && 'array' === $otherType) {
            return 0 === $this->compareArrays($this->getValue(), $other->getValue(), function ($a, $b) {
                return $a->isEqualTo($b);
            }, false);
        } else {
            return $this->getValue() == $other->getValue();
        }
    }

    public function isIdenticalTo(Zval $other) {
        $type = $this->getType();
        $otherType = $other->getType();
        if ('array' === $type && 'array' === $otherType) {
            return 0 === $this->compareArrays($this->getValue(), $other->getValue(), function ($a, $b) {
                return $a->isIdenticalTo($b);
            }, true);
        } elseif ($type == $otherType) {
            return $this->getValue() === $other->getValue();
        }
        return false;
    }

    private function compareArrays($a, $b, $callback, $ordered) {
        $result = count($a) - count($b);
        if (0 !== $result) {
            return $result;
        }
        if ($ordered) {
            reset($b);
        }
        foreach ($a as $keyA => $valueA) {
            if ($ordered) {
                if (key($b) !== $keyA) {
                    return 1;
                }
            } else {
                if (!isset($b[$keyA])) {
                    return 1;
                }
            }
            if (!$callback($valueA, $b[$keyA])) {
                return 1;
            }
            if ($ordered) {
                next($b);
            }
        }
        return 0;
    }
}
