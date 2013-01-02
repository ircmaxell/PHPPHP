<?php

namespace PHPPHP\Engine;

function PHP_array_pop(Executor $executor, array $args, Zval $return) {
    $args[0]->makeRef();
    $array = &$args[0]->getArray();
    $val = array_pop($array);
    $return->setValue($val);
}

function PHP_array_push(Executor $executor, array $args, Zval $return) {
    $args[0]->makeRef();
    $args[0] = &$args[0]->getArray();

    $val = call_user_func_array('array_push', $args);
    $return->setValue($val);
}

function PHP_count(Executor $executor, array $args, Zval $return) {
    $return->setValue(count($args[0]->toArray()));
}

function PHP_each(Executor $executor, array $args, Zval $return) {
    $args[0]->makeRef();
    $array = &$args[0]->getArray();
    $val = each($array);
    $return->setValue($val);
}

function PHP_reset(Executor $executor, array $args, Zval $return) {
    $args[0]->makeRef();
    $arg = $args[0];
    $array = &$arg->getArray();
    $return->setValue(reset($array));
}

function PHP_current(Executor $executor, array $args, Zval $return) {
    $arg = $args[0];
    $array = &$arg->getArray();
    $return->setValue(current($array));
}
