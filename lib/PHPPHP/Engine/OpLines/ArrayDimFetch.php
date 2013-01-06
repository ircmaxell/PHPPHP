<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class ArrayDimFetch extends \PHPPHP\Engine\OpLine {

    public $write = false;

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $key = $this->op2->getValue();
        if ($this->op1->isArray()) {
            $array =& $this->op1->getArray();
            if (!isset($array[$key])) {
                if ($this->write) {
                    $array[$key] = Zval::ptrFactory();
                    $this->result->setValue($array[$key]);
                } else {
                    $this->result->setValue(Zval::ptrFactory());
                }
            } else {
                $this->result->setValue($array[$key]);
            }
        } elseif ($this->op1->isString()) {
            $value = $this->op1->getValue();
            if (isset($value[$key])) {
                $this->result->setValue($value[$key]);
            } else {
                $this->result->setValue('');
            }
        } elseif ($this->write && $this->op1->isNull()) {
            $value = Zval::ptrFactory();
            $this->op1->setValue(array($key => $value));
            $this->result->setValue($value);
        } else {
            throw new \RuntimeException('Cannot use a scalar value as an array');
        }
        if ($this->write) {
            $this->result->getZval()->makeRef();
        }
        $data->nextOp();
    }

}
