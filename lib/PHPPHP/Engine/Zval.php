<?php

namespace PHPPHP\Engine;

abstract class Zval {

    const IS_NULL = 0;
    const IS_LONG = 1;
    const IS_DOUBLE = 2;
    const IS_BOOL = 3;
    const IS_ARRAY = 4;
    const IS_OBJECT = 5;
    const IS_STRING = 6;
    const IS_RESOURCE = 7;

    public static function factory($value = null) {
        $zval = new ZvalValue;
        $zval->value = $value;
        $zval->rebuildType();
        return $zval;
    }

    public static function ptrFactory($value = null) {
        if (!$value instanceof static) {
            $value = static::factory($value);
        }
        return new ZvalPtr($value);
    }
}