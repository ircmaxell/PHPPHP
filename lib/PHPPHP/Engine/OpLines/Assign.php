<?php

namespace PHPPHP\Engine\OpLines;

class Assign extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->op1->setValue($this->op2->getZval());

        if ($this->result) {
            $this->result->setValue($this->op2->getZval());
        }

        $data->nextOp();
    }

}