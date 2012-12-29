<?php

namespace PHPPHP\Engine\OpCodes;

class Jump implements \PHPPHP\Engine\OpCode {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $data->jump($data->opLine->op1->value);
    }

}