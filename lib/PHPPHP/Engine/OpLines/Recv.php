<?php

namespace PHPPHP\Engine\OpLines;

class Recv extends \PHPPHP\Engine\OpLine {
    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $args = $data->arguments;

        $n = (int) $this->op1->value;
        if (!isset($args[$n])) {
            throw new \Exception("Missing required argument $n");
        }

        $this->result->value = $args[$n]->value;
        $this->result->rebuildType();

        $data->nextOp();
    }
}