<?php

namespace PHPPHP\Engine\OpLines;

class StaticAssign extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $data->function->staticContext[$this->op1->toString()] = $this->op2;
        $data->symbolTable[$this->op1->toString()] = $this->op2;

        $data->nextOp();
    }

}   