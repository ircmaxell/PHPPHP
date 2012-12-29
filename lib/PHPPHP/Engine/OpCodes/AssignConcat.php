<?php

namespace PHPPHP\Engine\OpCodes;

use PHPPHP\Engine\Zval;

class AssignConcat implements \PHPPHP\Engine\OpCode {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $data->opLine->op1->value .= $data->opLine->op2->value;
        $data->opLine->op1->type = Zval::IS_STRING;
        $data->opLine->result->value = $data->opLine->op1->value;
        $data->opLine->result->type = $data->opLine->op1->type;

        $data->nextOp();
    }

}