<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class FunctionCall extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $functionCall = $data->executor->executorGlobals->call;
        $args = array();
        $stack = $data->executor->getStack();
        for ($i = $stack->count() - 1; $i >= 0; $i--) {
            $args[] = $stack->pop();
        }
        $args = array_reverse($args);
        
        $functionCall->getFunction()->execute($data->executor, $args, $this->result);
        $data->nextOp();
    }

}