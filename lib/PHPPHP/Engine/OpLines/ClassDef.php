<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\FunctionData;

class ClassDef extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $ce = $this->op1;

        $data->executor->getClassStore()->register($ce);

        $data->nextOp();
    }
}
