<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class IterateValues extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        if ($this->op2) {
            $this->op2->value = $this->op1->iterator->key();
            $this->op2->type = Zval::IS_STRING;
        }
        $this->result->zval->value = $this->op1->iterator->current()->zval->value;
        $this->result->rebuildType();
        $data->nextOp();
    }

}