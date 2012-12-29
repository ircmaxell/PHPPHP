<?php

namespace PHPPHP\Engine\OpCodes;

class Multiply implements \PHPPHP\Engine\OpCode {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $data->opLine->result->value = $data->opLine->op1->value * $data->opLine->op2->value;
        $data->opLine->result->rebuildType();

        $data->nextOp();
    }

}