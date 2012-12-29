<?php

namespace PHPPHP\Engine\OpCodes;

use PHPPHP\Engine\Executor;

class ReturnOp implements \PHPPHP\Engine\OpCode {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $data->returnValue->zval = $data->opLine->op1->zval;
        
        return Executor::DO_RETURN;
    }

}