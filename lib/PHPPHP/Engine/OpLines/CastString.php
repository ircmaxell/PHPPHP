<?php

namespace PHPPHP\Engine\OpLines;

class CastString extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->result->setValue($this->op1->toString());

        $data->nextOp();
    }

}