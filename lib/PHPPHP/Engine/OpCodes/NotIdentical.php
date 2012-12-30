<?php

namespace PHPPHP\Engine\OpCodes;

class NotIdentical implements \PHPPHP\Engine\OpCode {
    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $data->opLine->result->value = $data->opLine->op1->value !== $data->opLine->op2->value;
        $data->opLine->result->type = \PHPPHP\Engine\Zval::IS_BOOL;

        $data->nextOp();
    }
}