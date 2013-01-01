<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\FunctionData;

class FunctionDef extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $name = $this->op1->value;
        $opArray = $this->op2;

        $data->executor->getFunctionStore()->register(
            $name, new FunctionData\User($opArray)
        );

        $data->nextOp();
    }

}