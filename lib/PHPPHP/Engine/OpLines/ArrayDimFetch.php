<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class ArrayDimFetch extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $key = $this->op2->getValue();
        if ($this->op1->isArray()) {
            $ht = $this->op1->getHashTable();

            if (!$ht->exists($key)) {
                $new = Zval::ptrFactory();
                $ht->store($key, $new);
            }    
            $this->result->setValue($ht->get($key));
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