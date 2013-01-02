<?php

namespace PHPPHP\Engine;

function PHP_count(Executor $executor, array $args, Zval $return) {
    $return->setValue(count($args[0]->toArray()));
}

function PHP_define(Executor $executor, array $args) {
    $executor->getConstantStore()->register($args[0]->toString(), $args[1]->getZval());
}

function PHP_error_reporting(Executor $executor, array $args, Zval $return) {
    $return->setValue(0);
}

function PHP_func_get_arg(Executor $executor, array $args, Zval $return) {
    $num = $args[0]->toLong();
    $current = $executor->getCurrent();
    if (!$current->arguments || !isset($current->arguments[$num])) {
        $return->setValue(false);
    } else {
        $return->setValue($current->arguments[$num]);
    }
}

function PHP_func_get_args(Executor $executor, array $args, Zval $return) {
    $current = $executor->getCurrent();
    if ($current->arguments) {
        $return->setValue($current->arguments);
    } else {
        $return->setValue(false);
    }
}

function PHP_func_num_args(Executor $executor, array $args, Zval $return) {
    $current = $executor->getCurrent();
    if ($current->arguments) {
        $return->setValue(count($current->arguments));
    } else {
        $return->setValue(false);
    }
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

