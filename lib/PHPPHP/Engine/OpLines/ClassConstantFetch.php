<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class ClassConstantFetch extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {

        if ($this->op1->isString()) {
            $ce = $data->executor->getClassStore()->get($this->op1->getValue());
        } else if ($this->op1->isObject()) {
            $ce = $this->op1->getValue()->getClassEntry();
        } else {
            throw new \RuntimeException('Class name must be a valid object or a string');
        }

        $consts = $ce->getConstantStore();
        $value = $consts->get($this->op2->toString());

        $this->result->setValue($value);

        $data->nextOp();
    }

}
