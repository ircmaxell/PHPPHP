<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine;

class InitFCallByName extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $ci = $this->op1;
        $funcName = $this->op2->toString();
        if ($ci) {
            if ($ci->isObject()) {
                $ci = $ci->getValue();
                $functionData = $ci->getClassEntry()->getMethodStore()->get($funcName);
            } else {
                throw new \RuntimeException(sprintf('Call to a member function %s() on a non-object', $funcName));
            }
        } else {
            $functionData = $data->executor->getFunctionStore()->get($funcName);
        }
        
        $data->executor->executorGlobals->call = new Engine\FunctionCall($data->executor, $functionData, $ci);
        
        $data->nextOp();
    }

}