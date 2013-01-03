<?php

namespace PHPPHP\Engine;

function PHP_call_user_func(Executor $executor, array $args, Zval $return) {
    $executor->callCallback($args[0], $executor, $args, $return);
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

return array(
    'call_user_func' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $cb = array_shift($args);
            $executor->callCallback($cb, $executor, $args, $return);
        },
        false,
        array(
            new ParamData('callback', false, 'callable'),
        )
    ),
    'debug_backtrace' => new FunctionData\Internal(
        __NAMESPACE__ . '\PHP_debug_backtrace'
    ),
    'debug_print_backtrace' => new FunctionData\Internal(
        __NAMESPACE__ . '\PHP_debug_print_backtrace'
    ),
    'define' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $executor->getConstantStore()->register($args[0]->toString(), $args[1]->getZval());
        },
        false,
        array(
            new ParamData('name'),
            new ParamData('value'),
        )
    ),
    'error_reporting' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $return->setValue(0);
        }
    ),
    'func_get_arg' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $num = $args[0]->toLong();
            $current = $executor->getCurrent();
            if (!$current->arguments || !isset($current->arguments[$num])) {
                $return->setValue(false);
            } else {
                $return->setValue($current->arguments[$num]);
            }
        },
        false,
        array(
            new ParamData('arg_num'),
        )
    ),
    'func_get_args' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $current = $executor->getCurrent();
            if (is_array($current->arguments)) {
                $return->setValue($current->arguments);
            } else {
                $return->setValue(false);
            }
        }
    ),
    'func_num_args' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $current = $executor->getCurrent();
            if (is_array($current->arguments)) {
                $return->setValue(count($current->arguments));
            } else {
                $return->setValue(false);
            }
        }
    ),
    'function_exists' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $return->setValue($executor->getFunctionStore()->exists($args[0]->toString()));
        },
        false,
        array(
            new ParamData('function_name'),
        )
    ),
    'get_cfg_var' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            if ($args) {
                $return->setValue(null);
            } else {
                $return->setValue(array());
            }
        }
    ),
    'get_loaded_extensions' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $return->setValue(array());
        },
        false,
        array(
            new ParamData('callback', false, 'callable'),
            new ParamData('parameter', false, null, true),
        )
    )
);