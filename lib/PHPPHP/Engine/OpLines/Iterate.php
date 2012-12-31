<?php

namespace PHPPHP\Engine\OpLines;

class Iterate extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        if (!$this->op1->iterator instanceof \Traversable) {
            $this->op1->iterator = $this->op1->getIterator();
        } else {
            $this->op1->iterator->next();
        }
        if (!$this->op1->iterator->valid()) {
            $data->jumpTo($this->op2);
        } else {
            $data->nextOp();
        }
    }

}