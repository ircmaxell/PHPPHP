<?php

namespace PHPPHP\Engine;

abstract class Zval {

    public static function factory($value = null) {
        if ($value instanceof Zval\Value) {
            return $value;
        } elseif ($value instanceof Zval\Ptr) {
            return $value->getZval();
        }
        $zval = new Zval\Value($value);
        return $zval;
    }

    public static function ptrFactory($value = null) {
        if (!$value instanceof Zval) {
            $value = static::factory($value);
        }
        return new Zval\Ptr($value);
    }

    public static function lockedPtrFactory($value = null) {
        if (!$value instanceof Zval\Value) {
            $value = static::factory($value);
        }
        return new Zval\LockedPtr($value);
    }

    public static function variableFactory(Zval $name, Zval $class = null) {
        return new Zval\Variable($name, $class);
    }

    public static function iteratorFactory(\Traversible $iterator = null) {
        return new Zval\Iterator($iterator);
    }

}
