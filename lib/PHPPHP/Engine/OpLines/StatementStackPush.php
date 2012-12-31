<?php

namespace PHPPHP\Engine\OpLines;

class StatementStackPush extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        if (!$this->op1 instanceof StatementStackPop) {
            throw new \LogicException('Problem with compiler');
        }
        $data->statementStack[] = $this->op1;
        $data->nextOp();
    }

}