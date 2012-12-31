<?php

namespace PHPPHP\Engine\OpLines;

class FunctionCall extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $funcName = $this->op1->toString();
        $functionData = $data->executor->getFunctionStore()->get($funcName);
        $args = $this->op2->value;
        if (!is_array($args)) {
            $args = array();
        }
        $functionData->execute($args, $this->result);
        $data->nextOp();
    }

}