<?php

namespace PHPPHP\Engine\OpCodes;

class Concat implements \PHPPHP\Engine\OpCode {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $data->opLine->result->value = $data->opLine->op1->toString() . $data->opLine->op2->toString();
        $data->opLine->result->type = \PHPPHP\Engine\Zval::IS_STRING;

        $data->nextOp();
    }

}