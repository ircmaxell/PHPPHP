<?php

namespace PHPPHP\Engine\OpLines;

class NoOp extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $data->nextOp();
    }

}