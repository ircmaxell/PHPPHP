<?php

namespace PHPPHP\Engine\OpLines;

class CastInt extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->result->setValue($this->op1->toLong());

        $data->nextOp();
    }

}