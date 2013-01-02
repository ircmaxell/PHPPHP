<?php

namespace PHPPHP\Engine\OpLines;

class Mod extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        if (0 == $this->op2->getValue()) {
            $this->result->setValue(false);
        } else {
            $this->result->setValue($this->op1->getValue() % $this->op2->getValue());
        }

        $data->nextOp();
    }

}