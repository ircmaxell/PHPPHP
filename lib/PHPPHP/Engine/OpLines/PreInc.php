<?php

namespace PHPPHP\Engine\OpLines;

class PreInc extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->op1->setValue($this->op1->getValue() + 1);

        $this->result->setValue($this->op1);


        $data->nextOp();
    }

}