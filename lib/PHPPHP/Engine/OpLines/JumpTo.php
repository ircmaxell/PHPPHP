<?php

namespace PHPPHP\Engine\OpLines;

class JumpTo extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $data->jumpTo($data->opLine->op1);
    }

}