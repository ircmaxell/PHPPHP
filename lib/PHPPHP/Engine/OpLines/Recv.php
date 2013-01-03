<?php

namespace PHPPHP\Engine\OpLines;

class Recv extends \PHPPHP\Engine\OpLine {
    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $args = $data->arguments;

        $n = $this->op1->toLong();
        if (!isset($args[$n])) {
            throw new \Exception("Missing required argument $n");
        }
        $param = $data->function->getParam($n);
        if ($param) {
            $var = $data->fetchVariable($param->name);
            if ($param->isRef) {
                $var->assignZval($args[$n]->getZval());
                $var->addRef();
            } else {
                $var->setValue($args[$n]);
            }
        }

        $data->nextOp();
    }
}