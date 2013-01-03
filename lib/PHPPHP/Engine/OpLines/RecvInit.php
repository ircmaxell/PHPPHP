<?php

namespace PHPPHP\Engine\OpLines;

class RecvInit extends Recv {
    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $args = &$data->arguments;

        $n = $this->op1->toLong();
        if (!isset($args[$n])) {
            $args[$n] = Zval::ptrFactory($this->op2->getZval());
        }
        parent::execute($data);
    }
}