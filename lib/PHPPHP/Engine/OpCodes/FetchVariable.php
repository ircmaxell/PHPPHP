<?php

namespace PHPPHP\Engine\OpCodes;

use PHPPHP\Engine\Zval;

class FetchVariable implements \PHPPHP\Engine\OpCode {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $varName = $data->opLine->op1->toString();
        $data->opLine->result->zval = $data->fetchVariable($varName)->zval;
        $data->nextOp();
    }

}