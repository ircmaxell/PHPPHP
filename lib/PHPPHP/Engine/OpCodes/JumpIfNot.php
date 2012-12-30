<?php

namespace PHPPHP\Engine\OpCodes;

class JumpIfNot implements \PHPPHP\Engine\OpCode {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        if (!$data->opLine->op1->value) {
            $data->jumpTo($data->opLine->op2);
            return;
        }
        $data->nextOp();
    }

}