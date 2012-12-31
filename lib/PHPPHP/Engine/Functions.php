<?php

namespace PHPPHP\Engine;

function PHP_define(Executor $executor, array $args) {
    $executor->getConstantStore()->register($args[0]->toString(), $args[1]);
}

function PHP_error_reporting(Executor $executor, array $args, Zval $return) {
    $return->setValue(0);
}

function PHP_func_get_arg(Executor $executor, array $args, Zval $return) {
    $num = (int) $args[0]->value;
    $current = $executor->getCurrent();
    if (!$current->arguments || !isset($current->arguments[$num])) {
        $return->value = false;
    } else {
        $return->zval = Zval::factory($current->arguments[$num]);
    }
    $return->rebuildType();
}

function PHP_func_get_args(Executor $executor, array $args, Zval $return) {
    $current = $executor->getCurrent();
    if ($current->arguments) {
        $return->zval = Zval::factory($current->arguments);
    } else {
        $return->value = false;
    }
    $return->rebuildType();
}

function PHP_func_num_args(Executor $executor, array $args, Zval $return) {
    $current = $executor->getCurrent();
    if ($current->arguments) {
        $return->value = count($current->arguments);
    } else {
        $return->value = false;
    }
    $return->rebuildType();
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

