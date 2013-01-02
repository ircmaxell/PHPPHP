<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class FetchGlobalVariable extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $varName = $this->op1->toString();
        if (!isset($data->executor->executorGlobals->symbolTable[$varName])) {
            $data->executor->executorGlobals->symbolTable[$varName] = Zval::ptrFactory();
        }
        $data->symbolTable[$varName] = $data->executor->executorGlobals->symbolTable[$varName];
        $data->nextOp();
    }

}