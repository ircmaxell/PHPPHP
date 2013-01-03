<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class UnsetOp extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->op1->setValue(null);
        $data->nextOp();
    }

}