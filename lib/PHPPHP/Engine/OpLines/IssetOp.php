<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class IssetOp extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $varName = $this->op1->getName();
        $ret = isset($data->symbolTable[$varName]) && !$data->symbolTable[$varName]->isNull();
        $this->result->setValue($ret);
        $data->nextOp();
    }

}