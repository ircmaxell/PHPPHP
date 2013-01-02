<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class IterateValuesByRef extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        if ($this->op2) {
            $this->op2->setValue($this->op1->getIterator()->key());
        }
        $val = $this->op1->getIterator()->current();
        $val->makeRef();
        $this->result->forceValue($val);
        $data->nextOp();
    }

}