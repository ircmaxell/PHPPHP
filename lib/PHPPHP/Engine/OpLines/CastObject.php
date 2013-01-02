<?php

namespace PHPPHP\Engine\OpLines;

class CastObject extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->result->setValue($this->op1->toObject($data));

        $data->nextOp();
    }

}
