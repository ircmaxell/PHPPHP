<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class IssetOp extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->result->value = $this->op1->type != Zval::IS_NULL;
        $this->result->type = Zval::IS_BOOL;
        $data->nextOp();
    }

}