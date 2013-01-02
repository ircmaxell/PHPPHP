<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class AssignDiv extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        if (0 == $this->op2->getValue()) {
            $this->op1->setValue(false);
        } else {
            $this->op1->setValue($this->op1->getValue() / $this->op2->getValue());
        }
        $this->result->setValue($this->op1->getZval());

        $data->nextOp();
    }

}