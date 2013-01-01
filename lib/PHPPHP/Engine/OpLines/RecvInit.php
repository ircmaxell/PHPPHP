<?php

namespace PHPPHP\Engine\OpLines;

class RecvInit extends \PHPPHP\Engine\OpLine {
    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $args = $data->arguments;

        $n = (int) $this->op1->value;
        if (isset($args[$n])) {
            $this->result->value = $args[$n]->value;
        } else {
            $this->result->value = $this->op2->value;
        }

        $this->result->rebuildType();

        $data->nextOp();
    }
}