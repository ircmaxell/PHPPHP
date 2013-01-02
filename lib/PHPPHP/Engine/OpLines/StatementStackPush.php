<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\StructureData;

class StatementStackPush extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        if (!$this->op2 instanceof StatementStackPop) {
            throw new \LogicException('Problem with compiler');
        }
        $data->statementStack[] = new StructureData($this->op1, $this->op2);
        $data->nextOp();
    }

}