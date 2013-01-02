<?php

namespace PHPPHP\Engine\OpLines;

class PostInc extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $ret = $this->op1->getZval();
        $this->result->setValue($ret);

        $val = $this->op1->getValue();
        $this->op1->setValue(++$val);

        $data->nextOp();
    }

}