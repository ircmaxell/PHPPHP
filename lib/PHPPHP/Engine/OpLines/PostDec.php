<?php

namespace PHPPHP\Engine\OpLines;

class PostDec extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $ret = $this->op1->getZval();
        $this->result->setValue($ret);

        $this->op1->setValue($this->op1->getValue() - 1);

        $data->nextOp();
    }

}