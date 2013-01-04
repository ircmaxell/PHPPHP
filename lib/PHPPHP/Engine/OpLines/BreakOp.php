<?php

namespace PHPPHP\Engine\OpLines;

class BreakOp extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $data->jump($this->op1->breakOp);
    }

}