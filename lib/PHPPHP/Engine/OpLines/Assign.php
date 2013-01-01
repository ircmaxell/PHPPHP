<?php

namespace PHPPHP\Engine\OpLines;

class Assign extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->op1->value = $this->op2->value;
        $this->op1->type = $this->op2->type;

        if ($this->result) {
            $this->result->value = $this->op2->value;
            $this->result->type = $this->op2->type;
        }

        $data->nextOp();
    }

}