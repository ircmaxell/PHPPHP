<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Executor;

class ReturnOp extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        if ($data->function && $data->function->isByRef()) {
            $data->returnValue->makeRef();
        }
        if ($this->op1) {
            $data->returnValue->setValue($this->op1->getZval());
        } else {
            $data->returnValue->setValue(null);
        }

        return Executor::DO_RETURN;
    }

}