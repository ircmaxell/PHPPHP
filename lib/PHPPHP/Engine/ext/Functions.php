<?php

namespace PHPPHP\Engine;

function PHP_call_user_func(Executor $executor, array $args, Zval $return) {
    $cb = array_shift($args);
    if ($cb->isArray()) {
        $cbArray = $cb->getArray();
        $cbName = $cbArray[1]->toString();
        if ($cbArray[0]->isObject()) {
            $cbArray[0]->getValue()->callMethod($executor->getCurrent(), $cbName, $args, $return);
        } else {
            throw new \LogicException('Static methods are not supported yet');
        }
    } else {
        $cbName = $cb->toString();
        $func = $executor->getFunctionStore()->get($cbName);
        $func->execute($executor, $args, $return);
    }
}

function PHP_debug_backtrace(Executor $executor, array $args, Zval $return) {
    $array = array();
    $current = $executor->getCurrent();
    while ($current->parent) {
        $parent = $current->parent;
        $ret = array(
            'line' => $parent->opLine->attributes['startLine'],
            'file' => $parent->opArray->getFileName(),
        );
        if ($current->function) {
            if ($current->ci) {
                $ret['class'] = $current->ci->getClassEntry()->getName();
                $ret['object'] = $current->ci;
                $ret['type'] = '->';
                $ret['function'] = $current->ci->getClassEntry()->getMethodStore()->getName($current->function);
            } else {
                $ret['function'] = $current->executor->getFunctionStore()->getName($current->function);
            }
            $ret['args'] = $current->arguments;
        }
        $array[] = $ret;
        $current = $parent;
    }
    $return->setValue($array);
}

function PHP_debug_print_backtrace(Executor $executor, array $args) {
    $return = Zval::ptrFactory();
    PHP_debug_backtrace($executor, $args, $return);
    $output = $executor->getOutput();
    $frames = $return->toArray();
    foreach ($frames as $num => $stackFrame) {
        if (isset($stackFrame['function'])) {
            $class = isset($stackFrame['class']) ? $stackFrame['class'] : '';
            $class .= isset($stackFrame['type']) ? $stackFrame['type'] : '';
            $args = '';
            $sep = '';
            foreach ($stackFrame['args'] as $arg) {
                $args .= $sep . $arg->makePrintable()->getValue();
                $sep = ', ';
            }
            $line = "#$num $class{$stackFrame['function']}($args) called at [{$stackFrame['file']}:{$stackFrame['line']}]";
        } else {
            $line = "#$num include() [{$stackFrame['file']}:{$stackFrame['line']}]";
        }
        $output->write($line . "\n");
    } 
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
    if (is_array($current->arguments)) {
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

