<?php

namespace PHPPHP\Engine\OpLines;

class FetchConstant extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $consts = $data->executor->getConstantStore();
        $value = $consts->get($this->op1->toString());

        $this->result->value = $value->value;
        $this->result->type = $value->type;

        $data->nextOp();
    }

}