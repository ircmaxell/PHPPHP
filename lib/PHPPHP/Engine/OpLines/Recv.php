<?php

namespace PHPPHP\Engine\OpLines;

class Recv extends \PHPPHP\Engine\OpLine {
    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $args = $data->arguments;

        $n = $this->op1->toLong();
        if (!isset($args[$n])) {
            throw new \Exception("Missing required argument $n");
        }

        $this->result->setValue($args[$n]);

        $data->nextOp();
    }
}