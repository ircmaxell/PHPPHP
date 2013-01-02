<?php

namespace PHPPHP\Engine\OpLines;

class Iterate extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->result->setIterator($this->op1->getIterator());
        if (!$this->result->getIterator()->valid()) {
            $data->jump($this->op2);
        } else {
            $data->nextOp();
        }
    }

}