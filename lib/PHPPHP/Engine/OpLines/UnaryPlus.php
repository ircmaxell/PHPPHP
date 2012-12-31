<?php

namespace PHPPHP\Engine\OpLines;

class UnaryPlus extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->result->value = +$this->op1->value;
        $this->result->rebuildType();

        $data->nextOp();
    }

}