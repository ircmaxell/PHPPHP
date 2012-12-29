<?php

namespace PHPPHP\Engine;

class Functions {

    protected $coreFunctions = array(
        'strlen',
        'var_dump',
    );
    
    public function register(Executor $executor) {
        foreach ($this->coreFunctions as $funcName) {
            $this->registerCore($executor, $funcName);
        }
    }
    
    protected function registerCore(Executor $executor, $funcName) {
        $func = new FunctionData($executor, FunctionData::IS_INTERNAL);
        $func->callback = $funcName;
        $executor->addFunction($funcName, $func);
    }

}