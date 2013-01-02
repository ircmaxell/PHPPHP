<?php

namespace PHPPHP\Engine\OpLines;

class CastArray extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->result->setValue($this->op1->toArray());

        $data->nextOp();
    }

}