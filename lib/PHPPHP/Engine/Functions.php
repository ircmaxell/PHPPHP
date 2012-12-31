<?php

namespace PHPPHP\Engine;

function PHP_define(Executor $executor, array $args) {
    $executor->getConstantStore()->register($args[0]->toString(), $args[1]);
}

function PHP_error_reporting(Executor $executor, array $args, Zval $return) {
    $return->setValue(0);
}

function PHP_function_exists(Executor $executor, array $args, Zval $return) {
    $return->setValue($executor->getFunctionStore()->exists($args[0]->toString()));
}

function PHP_get_cfg_var(Executor $executor, array $args, Zval $return) {
    if ($args) {
        $return->setValue(null);
    } else {
        $return->setValue(array());
    }
}

function PHP_get_loaded_extensions(Executor $executor, array $args, Zval $return) {
    $return->setValue(array());
}

