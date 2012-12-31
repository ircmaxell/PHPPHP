<?php

namespace PHPPHP\Engine;

class Functions {

    protected $coreFunctions = array(
        'implode',
        'join',
        'php_uname',
        'phpversion',
        'realpath',
        'strlen',
        'var_dump',
        'zend_version',
    );
    
    public function register(Executor $executor) {
        foreach ($this->coreFunctions as $funcName) {
            $this->registerCore($executor, $funcName, $funcName);
        }
        $this->registerCustomFunctions($executor);
    }
    
    protected function registerCore(Executor $executor, $funcName, $callback) {
        $func = new FunctionData($executor, FunctionData::IS_INTERNAL);
        $func->callback = $callback;
        $executor->addFunction($funcName, $func);
    }

    protected function registerCustomFunctions(Executor $executor) {
        $this->registerCore($executor, 'get_cfg_var', function($var = null) {
            if ($var) {
                return null;
            } else {
                return array();
            }
        });
        $this->registerCore($executor, 'get_loaded_extensions', function() {
            return array();
        });
        $this->registerCore($executor, 'function_exists', function($funcName) use ($executor) {
            return $executor->hasFunction($funcName);
        });
    }

}