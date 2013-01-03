<?php

namespace PHPPHP\Engine\OpLines;

class AssignBitwiseOr extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->op1->setValue($this->op1->getValue() | $this->op2->getValue());
        $this->result->setValue($this->op1->getZval());

        $data->nextOp();
    }

}