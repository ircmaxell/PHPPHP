<?php

namespace PHPPHP\Engine\OpLines;

class IterateNext extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->op1->getIterator()->next();
        if (!$this->op1->getIterator()->valid()) {
            $data->nextOp();
        } else {
            $data->jump($this->op2);
        }
    }

}