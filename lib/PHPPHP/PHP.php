<?php

namespace PHPPHP;

use PHPPHP\Engine\Zval;

class PHP {

    protected $executor;

    public function __construct() {
        $functions = new Engine\FunctionStore;
        $constants = new Engine\ConstantStore;

        $this->executor = new Engine\Executor($functions, $constants);

        $this->registerCoreFunctions($functions);
        $this->registerCoreConstants($constants);
    }

    public function execute($code) {
        $opCodes = $this->executor->compile($code);
        $this->executor->execute($opCodes);
    }

    public function executeFile($file) {
        $this->executor->executorGlobals->cwd = dirname($file);
        $code = file_get_contents($file);
        $this->execute($code);
    }

    protected function registerCoreConstants(Engine\ConstantStore $constants) {
        $coreConstants = array(
            'null'  => null,
            'true'  => true,
            'false' => false,
        );

        foreach ($coreConstants as $name => $value) {
            $constants->register($name, Zval::factory($value), false);
        }
    }

    protected function registerCoreFunctions(Engine\FunctionStore $functions) {
        $coreFunctions = array(
            'strlen',
            'var_dump',
        );

        foreach ($coreFunctions as $funcName) {
            $func = new Engine\FunctionData($this->executor, Engine\FunctionData::IS_INTERNAL);
            $func->callback = $funcName;
            $functions->register($funcName, $func);
        }
    }
}