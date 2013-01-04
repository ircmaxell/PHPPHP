<?php

namespace PHPPHP\Engine\OpLines;

class Assign extends BinaryAssign {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->setValue($this->op2->getZval());
        $data->nextOp();
    }
}
