<?php

namespace PHPPHP\Engine\OpLines;

class EchoOp extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {

        $data->executor->getOutput()->write($this->op1->makePrintable()->toString());

        $data->nextOp();
    }

}