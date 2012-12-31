<?php

namespace PHPPHP\Engine\OpLines;

class Concat extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->result->value = $this->op1->toString() . $this->op2->toString();
        $this->result->type = \PHPPHP\Engine\Zval::IS_STRING;

        $data->nextOp();
    }

}