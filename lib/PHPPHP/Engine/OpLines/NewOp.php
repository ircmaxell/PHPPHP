<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class NewOp extends \PHPPHP\Engine\OpLine {

    protected static $instanceNumber = 0;

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        self::$instanceNumber++;
        $className = $this->op1->toString();
        $classEntry = $data->executor->getClassStore()->get($className);
        $args = $this->op2->toArray();
        $instance = $classEntry->instantiate($data, array(), $args);
        $instance->setInstanceNumber(self::$instanceNumber);
        $this->result->setValue(Zval::factory($instance));
        $data->nextOp();
    }
}
