<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class NewOp extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $className = $this->op1->toString();
        $classEntry = $data->executor->getClassStore()->get($className);
        $args = $this->op2->toArray();
        $instance = $classEntry->instanciate($data, [], $args);
        $this->result->setValue(Zval::factory($instance));
        $data->nextOp();
    }
}
