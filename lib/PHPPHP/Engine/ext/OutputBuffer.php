<?php

namespace PHPPHP\Engine;

function PHP_ob_clean(Executor $executor, array $args, Zval $return) {
    $buffer = $executor->getOutput();
    if ($buffer instanceof Output\Buffer) {
        $buffer->clean();
    }
}

function PHP_ob_end_clean(Executor $executor, array $args, Zval $return) {
    $buffer = $executor->getOutput();
    if ($buffer instanceof Output\Buffer) {
        $buffer->clean();
        $return->setValue(true);
    } else {
        $return->setValue(false);
    }
    $executor->setOutput($buffer->getParent());
}

function PHP_ob_end_flush(Executor $executor, array $args, Zval $return) {
    $buffer = $executor->getOutput();
    $buffer->endFlush();
    $executor->setOutput($buffer->getParent());
}

function PHP_ob_get_clean(Executor $executor, array $args, Zval $return) {
    $buffer = $executor->getOutput();
    if ($buffer instanceof Output\Buffer) {
        PHP_ob_get_contents($executor, $args, $return);
        $executor->setOutput($buffer->getParent());
    } else {
        $return->setValue(false);
    }
}

function PHP_ob_get_contents(Executor $executor, array $args, Zval $return) {
    $buffer = $executor->getOutput();
    if ($buffer instanceof Output\Buffer) {
        $return->setValue($buffer->getBuffer());
        $buffer->clean();
    } else {
        $return->setValue(false);
    }
}

function PHP_ob_get_flush(Executor $executor, array $args, Zval $return) {
    $buffer = $executor->getOutput();
    if ($buffer instanceof Output\Buffer) {
        $return->setValue($buffer->getBuffer());
        $buffer->flush();
    } else {
        $return->setValue(false);
    }
}

function PHP_ob_get_length(Executor $executor, array $args, Zval $return) {
    $buffer = $executor->getOutput();
    if ($buffer instanceof Output\Buffer) {
        $return->setValue(strlen($buffer->getBuffer()));
    } else {
        $return->setValue(false);
    }
}

function PHP_ob_get_level(Executor $executor, array $args, Zval $return) {
    $buffer = $executor->getOutput();
    $level = 0;
    while ($buffer instanceof Output\Buffer) {
        $buffer = $buffer->getParent();
        $level++;
    }
    $return->setValue($level);
}

function PHP_ob_list_handlers(Executor $executor, array $args, Zval $return) {
    $return->setValue(array('default output handler'));
}

function PHP_ob_flush(Executor $executor, array $args, Zval $return) {
    $buffer = $executor->getOutput();
    $buffer->flush();
}

function PHP_ob_start(Executor $executor, array $args, Zval $return) {
    $callback = null;
    if (isset($args[0])) {
        $callback = $executor->getFunctionStore()->get($args[0]->toString());
    }

    $executor->setOutput(new Output\Buffer($executor, $callback));
}

