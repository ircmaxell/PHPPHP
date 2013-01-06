<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class EmptyOp extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        if ($this->op1->isVariable()) {
            $varName = $this->op1->getName();
            if (!isset($data->symbolTable[$varName])) {
                $this->result->setValue(true);
                $data->nextOp();
                return;
            }
        }
        $this->result->setValue(!$this->op1->getValue());
        $data->nextOp();
    }

}
