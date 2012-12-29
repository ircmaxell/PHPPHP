<?php

namespace PHPPHP\Engine\OpCodes;

use PHPPHP\Engine\Zval;

class IssetOp implements \PHPPHP\Engine\OpCode {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $data->opLine->result->value = $data->opLine->op1->type != Zval::IS_NULL;
        $data->opLine->result->type = Zval::IS_BOOL;
        $data->nextOp();
    }

}