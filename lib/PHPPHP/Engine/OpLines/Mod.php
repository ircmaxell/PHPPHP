<?php

namespace PHPPHP\Engine\OpLines;

class Mod extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        if (0 == $this->op2->value) {
            $this->result->value = false;
        } else {
            $this->result->value = $this->op1->value % $this->op2->value;
        }
        $this->result->rebuildType();

        $data->nextOp();
    }

}