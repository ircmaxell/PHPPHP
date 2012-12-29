<?php

namespace PHPPHP\Engine\OpCodes;

use PHPPHP\Engine\Zval;
use PHPPHP\Engine\FunctionData;
use PHPPHP\Engine\ParamData;

class FunctionDef implements \PHPPHP\Engine\OpCode {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $funcName = $data->opLine->op1['name']->toString();
        $funcData = new FunctionData($data->executor, FunctionData::IS_USER);
        $funcData->opLines = $data->opLine->op1['stmts'];
        $params = array();
        foreach ($data->opLine->op1['params']->value as $param) {
            $paramData = new ParamData;
            $paramData->name = $param['name'];
            $paramData->default = $param['default'];
            $paramData->defaultOps = $param['ops'];
            $paramData->isRef = $param['isRef'];
            $paramData->type = $param['type'];
            $params[] = $paramData;
        }
        $funcData->params = $params;
        $data->executor->addFunction($funcName, $funcData);
        $data->nextOp();
    }

}