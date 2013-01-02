<?php

namespace PHPPHP\Engine\OpLines;

class PrintOp extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {

        $data->executor->getOutput()->write($this->op1->makePrintable()->toString());

        $this->result->setValue(1);

        $data->nextOp();
    }

}