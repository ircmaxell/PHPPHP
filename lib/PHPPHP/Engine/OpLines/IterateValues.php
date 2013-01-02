<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class IterateValues extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        if ($this->op2) {
            $this->op2->setValue($this->op1->getIterator()->key());
        }
        $this->result->setValue($this->op1->getIterator()->current());
        $data->nextOp();
    }

}