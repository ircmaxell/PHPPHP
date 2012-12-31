<?php

namespace PHPPHP\Engine\OpLines;

class StatementStackPop extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        array_pop($data->statementStack);
        $data->nextOp();
    }

}