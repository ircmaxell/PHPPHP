<?php

namespace PHPPHP\Engine\OpCodes;

class FetchConstant implements \PHPPHP\Engine\OpCode {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $consts = $data->executor->getConstantsStore();
        $value = $consts->get($data->opLine->op1->toString());

        $data->opLine->result->value = $value->value;
        $data->opLine->result->type = $value->type;

        $data->nextOp();
    }

}