<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class AssignConcat extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->op1->setValue($this->op1->getValue() . $this->op2->getValue());
        if ($this->result) {
            $this->result->setValue($this->op1->getZval());
        }

        $data->nextOp();
    }

}