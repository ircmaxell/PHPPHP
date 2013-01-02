<?php

namespace PHPPHP\Engine;

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

