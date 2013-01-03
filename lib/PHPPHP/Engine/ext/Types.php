<?php

namespace PHPPHP\Engine;

function gettypeBuilder($type) {
    return new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) use ($type) {
            $return->setValue($type === $args[0]->getType());
        },
        false,
        array(new ParamData('var'))
    );
}

return array(
    'is_array' => gettypeBuilder('array'),
    'is_bool' => gettypeBuilder('boolean'),
    'is_double' => gettypeBuilder('double'),
    'is_float' => gettypeBuilder('double'),
    'is_int' => gettypeBuilder('integer'),
    'is_integer' => gettypeBuilder('integer'),
    'is_long' => gettypeBuilder('integer'),
    'is_null' => gettypeBuilder('NULL'),
    'is_object' => gettypeBuilder('object'),
    'is_real' => gettypeBuilder('double'),
    'is_string' => gettypeBuilder('string'),
    'is_numeric' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $return->setValue(is_numeric($args[0]->getValue()));
        },
        false,
        array(new ParamData('var'))
    ),
    'is_scalar' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $return->setValue(!$args[0]->isObject() && !$args[0]->isArray());
        },
        false,
        array(new ParamData('var'))
    ),
);