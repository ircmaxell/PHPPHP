<?php

namespace PHPPHP\Engine\OpLines;

class ShellExec extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $command = $this->op1->getValue();
        $result = `$command`;
        $this->result->setValue($result);

        $data->nextOp();
    }

}