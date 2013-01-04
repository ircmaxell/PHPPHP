<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\FunctionCall;

class InitFCallByName extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $ci = $this->op1;
        $funcName = $this->op2->toString();
        if ($ci) {
            if ($ci->isObject()) {
                $ci = $ci->getValue();
                $functionData = $ci->getClassEntry()->getMethodStore()->get($funcName);
            } elseif ($ci->isString()) {
                throw new \LogicException('static method calls not implemented yet');
            } else {
                throw new \LogicException('Invalid opcode type: ' . $ci->getType());
            }
        } else {
            $functionData = $data->executor->getFunctionStore()->get($funcName);
        }
        
        $data->executor->executorGlobals->call = new FunctionCall($data->executor, $functionData, $ci);
        
        $data->nextOp();
    }

}