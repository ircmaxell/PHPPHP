<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class ArrayDimFetch extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $key = $this->op2->getValue();
        if ($this->op1->isArray()) {
            $array = $this->op1->toArray();
            if (!isset($array[$key])) {
                $array[$key] = Zval::ptrFactory();
            }
            $this->result->setValue($array[$key]);
        } elseif ($this->op1->isString()) {
            $value = $this->op1->getValue();
            if (isset($value[$key])) {
                $this->result->setValue($value[$key]);
            } else {
                $this->result->setValue('');
            }
        }
        $data->nextOp();
    }

}