<?php

namespace PHPPHP\Engine\OpLines;

class Concat extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->result->setValue($this->op1->toString() . $this->op2->toString());

        $data->nextOp();
    }

}