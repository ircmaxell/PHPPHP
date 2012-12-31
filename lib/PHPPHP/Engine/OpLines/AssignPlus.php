<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class AssignPlus extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->op1->value += $this->op2->value;
        $this->op1->rebuildType();
        $this->result->value = $this->op1->value;
        $this->result->type = $this->op1->type;

        $data->nextOp();
    }

}