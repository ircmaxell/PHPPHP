<?php

namespace PHPPHP\Engine\OpLines;

class Multiply extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->result->setValue($this->op1->getValue() * $this->op2->getValue());

        $data->nextOp();
    }

}