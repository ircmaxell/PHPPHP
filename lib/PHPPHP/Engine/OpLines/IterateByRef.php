<?php

namespace PHPPHP\Engine\OpLines;

class IterateByRef extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->op1->makeRef();
        $this->result->setIterator($this->op1->getIterator());
        if (!$this->result->getIterator()->valid()) {
            $data->jump($this->op2);
        } else {
            $data->nextOp();
        }
    }

}