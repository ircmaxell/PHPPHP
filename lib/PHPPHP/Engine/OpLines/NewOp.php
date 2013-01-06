<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;
use PHPPHP\Engine;

class NewOp extends \PHPPHP\Engine\OpLine {

    public $noConstructorJumpOffset;

    protected static $instanceNumber = 0;

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        self::$instanceNumber++;
        $className = $this->op1->toString();
        $classEntry = $data->executor->getClassStore()->get($className);
        $instance = $classEntry->instantiate($data, array());
        $instance->setInstanceNumber(self::$instanceNumber);
        $constructor = $classEntry->getConstructor();
        if ($constructor) {
            $data->executor->executorGlobals->call = new Engine\FunctionCall($data->executor, $constructor, $instance);
        }
        $this->result->setValue(Zval::factory($instance));
        if (!$constructor) {
            $data->jump($this->noConstructorJumpOffset);
        } else {
            $data->nextOp();
        }
    }
}
