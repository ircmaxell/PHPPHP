<?php

namespace PHPPHP\Engine;

return array(
    'ob_clean' => new FunctionData\Internal(
        function(Executor $executor) {
            $buffer = $executor->getOutput();
            if ($buffer instanceof Output\Buffer) {
                $buffer->clean();
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
        function(Executor $executor) {
            $buffer = $executor->getOutput();
            if ($buffer instanceof Output\Buffer) {
                $buffer->endFlush();
                $executor->setOutput($buffer->getParent());
            }
        }
    ),
    'ob_get_clean' => new FunctionData\Internal(
        function(Executor $executor) {
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
        function(Executor $executor) {
            $buffer = $executor->getOutput();
            if ($buffer instanceof Output\Buffer) {
                $return->setValue($buffer->getBuffer());
            } else {
                $return->setValue(false);
            }
        }
    ),
    'ob_get_contents' => new FunctionData\Internal(
        function(Executor $executor) {
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
        function(Executor $executor) {
            $buffer = $executor->getOutput();
            if ($buffer instanceof Output\Buffer) {
                $return->setValue(strlen($buffer->getBuffer()));
            } else {
                $return->setValue(false);
            }
        }
    ),
    'ob_get_level' => new FunctionData\Internal(
        function(Executor $executor) {
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
        function(Executor $executor) {
            $return->setValue(array('default output handler'));
        }
    ),
    'ob_flush' => new FunctionData\Internal(
        function(Executor $executor) {
            $buffer = $executor->getOutput();
            $buffer->flush();
        }
    ),
    'ob_start' => new FunctionData\Internal(
        function(Executor $executor, array $args) {
            $callback = null;
            if (isset($args[0])) {
                $callback = $executor->getCallback($args[0]);
            }
            $executor->setOutput(new Output\Buffer($executor, $callback));
        },
        false,
        array(
            new ParamData('output_callback', false, 'callable', true),
            new ParamData('chunk_size', false, null, true),
            new ParamData('erase', false, null, true),
        )
    ),
);