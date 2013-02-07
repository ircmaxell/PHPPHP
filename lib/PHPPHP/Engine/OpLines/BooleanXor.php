<?php

namespace PHPPHP\Engine\OpLines;

class BooleanXor extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->result->setValue($this->op1->getValue() xor $this->op2->getValue());

        $data->nextOp();
    }

}