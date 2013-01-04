<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class AssignDim extends \PHPPHP\Engine\OpLine {
 
    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->op1->separateIfNotRef();
        if (!$this->op1->isArray()) {
            if ($this->op1->isNull()) {
                $this->op1->setValue(array());
            } else {
                throw new \RuntimeException('Cannot use a scalar value as an array');
            }
        }
        $array =& $this->op1->getArray();
        if ($this->dim) {
            $key = $this->dim->toString();
            if (isset($array[$key])) {
                $array[$key]->forceValue($this->op2->getZval());
            } else {
                $array[$key] = Zval::ptrFactory($this->op2->getZval());
            }
        } else {
            $array[] = Zval::ptrFactory($this->op2->getZval());
        }
        if ($this->result) {
            $this->result->setValue($this->op2->getZval());
        }

        $data->nextOp();
    }
}
