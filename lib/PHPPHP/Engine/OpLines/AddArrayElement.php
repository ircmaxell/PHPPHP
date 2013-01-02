<?php

namespace PHPPHP\Engine\OpLines;

class AddArrayElement extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $key = $this->op1->toString();
        if ($key) {
            $this->result->getHashTable()->store($key, $this->op2);
        } else {
            $this->result->getHashTable()->append($this->op2);
        }
        $data->nextOp();
    }

}