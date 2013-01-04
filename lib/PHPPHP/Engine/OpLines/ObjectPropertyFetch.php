<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class ObjectPropertyFetch extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $key = $this->op2->toString();
        if (!$this->op1->isObject()) {
            $this->op1->setValue($this->op1->toObject($data));
        }
        $prop = $this->op1->getValue()->getProperty($key);
        $this->result->assignZval($prop);

        $data->nextOp();
    }

}
