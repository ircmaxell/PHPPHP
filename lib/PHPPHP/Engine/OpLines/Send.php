<?php

namespace PHPPHP\Engine\OpLines;

class Send extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->result->assignValue($this->op1);

        $data->nextOp();
    }

}