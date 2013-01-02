<?php

namespace PHPPHP\Engine\OpLines;

class RecvInit extends \PHPPHP\Engine\OpLine {
    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $args = $data->arguments;

        $n = $this->op1->toLong();
        if (isset($args[$n])) {
            $this->result->setValue($args[$n]);
        } else {
            $this->result->setValue($this->op2);
        }

        $data->nextOp();
    }
}