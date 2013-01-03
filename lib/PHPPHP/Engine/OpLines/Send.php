<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class Send extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        
        if($data->executor->executorGlobals->call->getFunction()->isArgByRef($this->op2)) {
            if ($this->op1->isVariable()) {
                $this->op1->makeRef();
            } else {
                throw new \RuntimeException("Can't pass parameter {$this->op2} by reference");
            }
        }
        $data->executor->getStack()->push(Zval::ptrFactory($this->op1->getZval()));

        $data->nextOp();
    }

}