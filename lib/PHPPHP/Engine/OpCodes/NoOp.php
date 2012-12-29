<?php

namespace PHPPHP\Engine\OpCodes;

class NoOp implements \PHPPHP\Engine\OpCode {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $data->nextOp();
    }

}