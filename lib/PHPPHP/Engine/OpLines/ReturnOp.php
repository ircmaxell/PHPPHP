<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Executor;

class ReturnOp extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $data->returnValue->zval = $this->op1->zval;

        return Executor::DO_RETURN;
    }

}