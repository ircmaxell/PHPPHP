<?php

namespace PHPPHP\Engine\OpLines;

class PreDec extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->op1->value--;
        $this->result->value = $this->op1->value;
        $this->result->type = $this->op1->type;

        $data->nextOp();
    }

}