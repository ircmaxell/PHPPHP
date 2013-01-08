<?php

namespace PHPPHP\Engine;

return array(
    'ob_clean' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $buffer = $executor->getOutput();
            if ($buffer instanceof Output\Buffer) {
                $buffer->clean();
                $return->setValue(true);
            } else {
                $executor->raiseError(E_NOTICE, 'ob_clean(): failed to delete buffer. No buffer to delete');
                $return->setValue(false);
            }
        }
    ),
    'ob_end_clean' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $buffer = $executor->getOutput();
            if ($buffer instanceof Output\Buffer) {
                $buffer->clean();
                $return->setValue(true);
                $executor->setOutput($buffer->getParent());
            } else {
                $return->setValue(false);
            }
        }
    ),
    'ob_end_flush' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $buffer = $executor->getOutput();
            if ($buffer instanceof Output\Buffer) {
                $buffer->endFlush();
                $executor->setOutput($buffer->getParent());
            }
        }
    ),
    'ob_get_clean' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $buffer = $executor->getOutput();
            if ($buffer instanceof Output\Buffer) {
                $return->setValue($buffer->getBuffer());
                $buffer->setBuffer('');
            } else {
                $return->setValue(false);
            }
        }
    ),
    'ob_get_contents' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $buffer = $executor->getOutput();
            if ($buffer instanceof Output\Buffer) {
                $return->setValue($buffer->getBuffer());
            } else {
                $return->setValue(false);
            }
        }
    ),
    'ob_get_contents' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $buffer = $executor->getOutput();
            if ($buffer instanceof Output\Buffer) {
                $return->setValue($buffer->getBuffer());
            } else {
                $return->setValue(false);
            }
        }
    ),
    'ob_get_flush' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $buffer = $executor->getOutput();
            if ($buffer instanceof Output\Buffer) {
                $return->setValue($buffer->getBuffer());
                $buffer->flush();
            } else {
                $return->setValue(false);
            }
        }
    ),
    'ob_get_length' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $buffer = $executor->getOutput();
            if ($buffer instanceof Output\Buffer) {
                $return->setValue(strlen($buffer->getBuffer()));
            } else {
                $return->setValue(false);
            }
        }
    ),
    'ob_get_level' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $buffer = $executor->getOutput();
            $level = 0;
            while ($buffer instanceof Output\Buffer) {
                $buffer = $buffer->getParent();
                $level++;
            }
            $return->setValue($level);
        }
    ),
    'ob_list_handlers' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $ret = array();
            $buffer = $executor->getOutput();
            do {
                if ($buffer instanceof Output\Buffer) {
                    $cb = $buffer->getCallback();
                    if ($cb) {
                        $ret[] = Zval::ptrFactory('callback');
                        continue;
                    }
                    $ret[] = Zval::ptrFactory('default output handler');
                }
            } while ($buffer = $buffer->getParent());
            $return->setValue(array_reverse($ret));
        }
    ),
    'ob_flush' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $buffer = $executor->getOutput();
            try {
                $buffer->flush();
                $return->setValue(true);
            } catch (\LogicException $e) {
                if ($e->getMessage() == 'Unflushable Buffer') {
                    $executor->raiseError(E_NOTICE, 'failed to flush buffer of callback (0)');
                    $return->setValue(false);
                } else {
                    var_dump($e->getMessage());
                    throw $e;
                }
            }
        }
    ),
    'ob_start' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $output = $executor->getOutput();
            if ($output instanceof Output\ReadOnly) {
                $executor->raiseError(E_ERROR, 'Cannot use output buffering in output buffering display handlers');
            }
            $callback = null;
            if (isset($args[0])) {
                $callback = $executor->getCallback($args[0]);
            }
            if (!empty($args[2]) && !$args[2]->toBool()) {
                $executor->setOutput(new Output\UnerasableBuffer($executor, $callback));
            } else {
                $executor->setOutput(new Output\Buffer($executor, $callback));
            }
        },
        false,
        array(
            new ParamData('output_callback', false, 'callable', true),
            new ParamData('chunk_size', false, null, true),
            new ParamData('erase', false, null, true),
        )
    ),
);