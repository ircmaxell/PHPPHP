<?php

namespace PHPPHP\Engine;

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
            $cb = $executor->getCallback($cb);
            $cb($executor, $args, $return);
        },
        false,
        array(
            new ParamData('callback', false, 'callable'),
            new ParamData('...', false, null, true),
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
    'func_get_arg' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $type = $args[0]->getType();
            if ($type != 'integer') {
                $executor->raiseError(E_WARNING, 'func_get_arg() expects parameter 1 to be long, ' . $type . ' given', '', false);
                return;
            }

            $num = $args[0]->toLong();
            $current = $executor->getCurrent();
            if ($num < 0) {
                $executor->raiseError(E_WARNING, ' The argument number should be >= 0');
                $return->setValue(false);
            } elseif (!$current->function) {
                $executor->raiseError(E_WARNING, ' Called from the global scope - no function context');
                $return->setValue(false);
            } elseif (!isset($current->arguments[$num])) {
                $executor->raiseError(E_WARNING, ' Argument ' . $num . ' not passed to function');
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
            if ($current->function && is_array($current->arguments)) {
                $return->setValue($current->arguments);
            } else {
                $executor->raiseError(E_WARNING, ' Called from the global scope - no function context');
                $return->setValue(false);
            }
        }
    ),
    'func_num_args' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $current = $executor->getCurrent();
            if ($current->function && is_array($current->arguments)) {
                $return->setValue(count($current->arguments));
            } else {
                $executor->raiseError(E_WARNING, ' Called from the global scope - no function context');
                $return->setValue(-1);
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
        },
        false,
        array(
            new ParamData('varName'),
        )
    ),
    'get_declared_classes' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $names = array();
            foreach ($executor->getClassStore()->getNames() as $name) {
                $names[] = Zval::ptrFactory($name);
            }
            $return->setValue($names);
        }
    ),
    'get_loaded_extensions' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $extensions = $executor->getExtensions();
            $ret = array();
            $internal = isset($args[0]) ? $args[0]->toBool() : false;
            foreach ($extensions as $ext) {
                if (!$internal XOR $ext->isInternal()) {
                    $ret[] = Zval::ptrFactory($ext->getName());
                }
            }
            $return->setValue($ret);
        },
        false,
        array(
            new ParamData('zend_extensions', false, null, true),
        )
    )
);