<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class StaticAssign extends \PHPPHP\Engine\OpLine {

    protected $value;

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $varName = $this->op1->toString();
        $var = $data->fetchVariable($varName);
        if (!$this->value) {
            $var->makeRef();
            $this->value = $var;
            if ($this->op2) {
                $var->setValue($this->op2);
            }
        }
        $var->assignZval($this->value);

        $data->nextOp();
    }

}   