<?php

namespace PHPPHP\Engine\OpLines;

class FetchVariable extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $varName = $this->op1->toString();
        $this->result->zval = $data->fetchVariable($varName)->zval;
        $data->nextOp();
    }

}