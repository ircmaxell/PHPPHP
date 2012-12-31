<?php

namespace PHPPHP\Engine\OpLines;

class EchoOp extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {

        echo $this->op1->makePrintable()->value;

        $data->nextOp();
    }

}