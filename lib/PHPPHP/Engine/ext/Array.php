<?php

namespace PHPPHP\Engine;

return array(
    'array_pop' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $array = &$args[0]->getArray();
            $val = array_pop($array);
            $return->setValue($val);
        },
        false,
        array(
            new ParamData('array', true, 'array'),
        )
    ),
    'array_push' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $args[0] = &$args[0]->getArray();
            $val = call_user_func_array('array_push', $args);
            $return->setValue($val);
        },
        false,
        array(
            new ParamData('array', true, 'array'),
            new ParamData('var'),
        )
    ),
    'count' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $mode = isset($args[1]) ? $args[1]->toLong() : COUNT_NORMAL;
            $return->setValue(count($args[0]->toArray(), $mode));
        },
        false,
        array(
            new ParamData('array'),
            new ParamData('mode', false, null, true),
        )
    ),
    'current' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $array = &$args[0]->getArray();
            $return->setValue(current($array));
        },
        false,
        array(
            new ParamData('array', true, 'array'),
        )
    ),
    'each' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $array = &$args[0]->getArray();
            $return->setValue(each($array));
        },
        false,
        array(
            new ParamData('array', true, 'array'),
        )
    ),
    'reset' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $array = &$args[0]->getArray();
            $return->setValue(reset($array));
        },
        false,
        array(
            new ParamData('array', true, 'array'),
        )
    ),
);