<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Executor;

class ExitOp extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        if ($this->op1->value) {
            echo $this->op1->toString();
        }
        return Executor::DO_SHUTDOWN;
    }

}