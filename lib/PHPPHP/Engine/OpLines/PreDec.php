<?php

namespace PHPPHP\Engine\OpLines;

class PreDec extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $val = $this->op1->getValue();
        $this->op1->setValue(--$val);

        $this->result->setValue($this->op1);

        $data->nextOp();
    }

}