<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class Send extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $ptr = null;
        if($data->executor->executorGlobals->call->getFunction()->isArgByRef($this->op2)) {
            if ($this->op1->isVariable()) {
                $this->op1->makeRef();
                $ptr = Zval::ptrFactory($this->op1->getZval());
            } else {
                throw new \RuntimeException("Can't pass parameter {$this->op2} by reference");
            }
        } else {
            $ptr = Zval::ptrFactory($this->op1->getValue());
        }
        $data->executor->getStack()->push($ptr);

        $data->nextOp();
    }

}