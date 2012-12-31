<?php

namespace PHPPHP\Engine\OpLines;

class IterateNext extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->op1->iterator->next();
        if (!$this->op1->iterator->valid()) {
            $data->nextOp();
        } else {
            $data->jumpTo($this->op2);
        }
    }

}