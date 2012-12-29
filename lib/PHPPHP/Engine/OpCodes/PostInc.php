<?php

namespace PHPPHP\Engine\OpCodes;

class PostInc implements \PHPPHP\Engine\OpCode {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $ret = $data->opLine->op1->value;
        $data->opLine->op1->value++;
        $data->opLine->result->value = $ret;
        $data->opLine->result->type = $data->opLine->op1->type;

        $data->nextOp();
    }

}