<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class Send extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $ptr = null;
        if($data->executor->executorGlobals->call->getFunction()->isArgByRef($this->op2->getValue())) {
            if ($this->op1->isVariable() || $this->op1->isRef() || $this->op1->isObject()) {
                $op = $this->op1->getPtr();
                $op->makeRef();
                $op->addRef();
                $ptr = Zval::ptrFactory($op->getZval());
            } else {
                throw new \RuntimeException("Can't pass parameter {" . $this->op2->getValue() . "} by reference");
            }
        } else {
            $ptr = Zval::ptrFactory($this->op1->getValue());
        }
        $data->executor->getStack()->push($ptr);

        $data->nextOp();
    }

}