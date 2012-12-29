<?php

namespace PHPPHP\Engine\OpCodes;

class EchoOp implements \PHPPHP\Engine\OpCode {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {

        echo $data->opLine->op1->makePrintable()->value;

        $data->nextOp();
    }

}