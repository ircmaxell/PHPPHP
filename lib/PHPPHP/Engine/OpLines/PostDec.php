<?php

namespace PHPPHP\Engine\OpLines;

class PostDec extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $ret = $this->op1->value;
        $this->op1->value--;
        $this->result->value = $ret;
        $this->result->type = $this->op1->type;

        $data->nextOp();
    }

}