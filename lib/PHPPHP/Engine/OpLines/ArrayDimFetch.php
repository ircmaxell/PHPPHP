<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class ArrayDimFetch extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $key = $this->op2->toString();
        if ($this->op1->type == Zval::IS_ARRAY) {
            if (!isset($this->op1->zval->value[$key])) {
                $this->op1->zval->value[$key] = Zval::ptrFactory();
            }    
            $this->result->zval = $this->op1->zval->value[$key]->zval;
        } elseif ($this->op1->type == Zval::IS_STRING) {
            if (isset($this->op1->zval->value[$key])) {
                $this->result->zval = Zval::factory($this->op1->zval->value[$key]);
            } else {
                $this->result->zval = Zval::factory('');
            }
        }
        $data->nextOp();
    }

}