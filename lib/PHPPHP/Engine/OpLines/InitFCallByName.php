<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\FunctionCall;

class InitFCallByName extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $ci = $this->op1;
        $funcName = $this->op2->toString();
        if ($ci) {
            $functionData = $ci->getClassEntry()->getMethodStore()->get($funcName);
        } else {
            $functionData = $data->executor->getFunctionStore()->get($funcName);
        }
        
        $data->executor->executorGlobals->call = new FunctionCall($data->executor, $functionData, $ci);
        
        $data->nextOp();
    }

}