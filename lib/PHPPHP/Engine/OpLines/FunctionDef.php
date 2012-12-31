<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\FunctionData;
use PHPPHP\Engine\ParamData;

class FunctionDef extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $origParams = $data->opLine->op1['params']->value ?: array();

        $params = array();
        foreach ($origParams as $param) {
            $paramData = new ParamData;
            $paramData->name = $param['name'];
            $paramData->default = $param['default'];
            $paramData->defaultOps = $param['ops'];
            $paramData->isRef = $param['isRef'];
            $paramData->type = $param['type'];
            $params[] = $paramData;
        }

        $data->executor->getFunctionStore()->register(
            $data->opLine->op1['name']->toString(),
            new FunctionData\User($data->opLine->op1['stmts'], $params)
        );

        $data->nextOp();
    }

}