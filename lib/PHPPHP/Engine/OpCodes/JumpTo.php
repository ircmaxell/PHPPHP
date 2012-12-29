<?php

namespace PHPPHP\Engine\OpCodes;

class JumpTo implements \PHPPHP\Engine\OpCode {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $data->jumpTo($data->opLine->op1);
    }

}