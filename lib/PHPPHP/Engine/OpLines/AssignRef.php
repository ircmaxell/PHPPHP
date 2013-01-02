<?php

namespace PHPPHP\Engine\OpLines;

class AssignRef extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->op2->makeRef();
        $this->op1->forceValue($this->op2->getZval());

        if ($this->result) {
            $this->result->setValue($this->op2->getValue());
        }

        $data->nextOp();
    }

}