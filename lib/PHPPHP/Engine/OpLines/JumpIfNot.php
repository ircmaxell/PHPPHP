<?php

namespace PHPPHP\Engine\OpLines;

class JumpIfNot extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        if (!$this->op1->toBool()) {
            $data->jump($this->op2);
            return;
        }
        $data->nextOp();
    }

}