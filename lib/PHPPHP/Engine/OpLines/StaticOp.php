<?php

namespace PHPPHP\Engine\OpLines;

class StaticOp extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        if ($data->function && $data->function->staticContext) {
            $data->jumpTo($this->op1);
            return;
        }

        $data->nextOp();
    }

}