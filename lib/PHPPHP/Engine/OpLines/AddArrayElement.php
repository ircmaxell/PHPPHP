<?php

namespace PHPPHP\Engine\OpLines;

class AddArrayElement extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $key = $this->op1->toString();
        if ($key) {
            $this->result->zval->value[$key] = $this->op2;
        } else {
            $this->result->zval->value[] = $this->op2;
        }
        $data->nextOp();
    }

}