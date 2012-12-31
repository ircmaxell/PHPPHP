<?php

namespace PHPPHP\Engine\OpLines;

class NotIdentical extends \PHPPHP\Engine\OpLine {
    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->result->value = $this->op1->value !== $this->op2->value;
        $this->result->type = \PHPPHP\Engine\Zval::IS_BOOL;

        $data->nextOp();
    }
}