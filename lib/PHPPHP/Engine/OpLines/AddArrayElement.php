<?php

namespace PHPPHP\Engine\OpLines;

class AddArrayElement extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $key = $this->op1->toString();
        $array = $this->result->toArray();
        if ($key) {
            $array[$key] = $this->op2;
        } else {
            $array[] = $this->op2;
        }
        $this->result->setValue($array);
        $data->nextOp();
    }

}