<?php

namespace PHPPHP\Engine\OpCodes;

use PHPPHP\Engine\Zval;

class FunctionCall implements \PHPPHP\Engine\OpCode {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $funcName = $data->opLine->op1->toString();
        $functionData = $data->executor->getFunction($funcName);
        $args = $data->opLine->op2->value;
        if (!is_array($args)) {
            $args = array();
        }
        $functionData->execute($args, $data->opLine->result);
        $data->nextOp();
    }

}