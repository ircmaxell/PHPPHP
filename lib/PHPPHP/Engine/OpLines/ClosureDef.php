<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\FunctionData;

use PHPPHP\Engine\Zval;

class ClosureDef extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $name = $this->op1->toString();
        $functionData = $this->op2;
        $ce = $data->executor->getClassStore()->get('Closure');
        $ci = $ce->instantiate($data, array('functionData' => Zval::ptrFactory($functionData)));
        if ($this->result) {
        	$this->result->setValue($ci);
        }

        $data->nextOp();
    }

}