<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine;

class InitStaticMethodCall extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $className = $this->op1;
        $funcName = $this->op2->toString();
        if ($className->isString()) {
            $ce = $data->executor->getClassStore()->get($className->getValue());
        } else if ($className->isObject()) {
            $ce = $className->getValue()->getClassEntry();
        } else {
            throw new \RuntimeException('Class name must be a valid object or a string');
        }

        $functionData = $ce->getMethodStore()->get($funcName);
        
        $data->executor->executorGlobals->call = new Engine\FunctionCall($data->executor, $functionData, null, $ce);
        
        $data->nextOp();
    }

}
