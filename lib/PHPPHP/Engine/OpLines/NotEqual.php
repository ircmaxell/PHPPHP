<?php

namespace PHPPHP\Engine\OpLines;

class NotEqual extends \PHPPHP\Engine\OpLine {
    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->result->setValue(!$this->op1->isEqualTo($this->op2));

        $data->nextOp();
    }
}
