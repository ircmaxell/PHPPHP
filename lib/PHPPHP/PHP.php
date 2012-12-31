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
        $this->registerCustomFunctions($functions);
        $this->registerCoreConstants($constants);
    }

    public function execute($code) {
        $opCodes = $this->executor->compile($code);
        $retval = $this->executor->execute($opCodes);
        if ($retval) {
            return $retval->value;
        }
        return null;
    }

    public function executeFile($file) {
        if (empty($file)) {
            return;
        }
        $this->executor->executorGlobals->cwd = dirname($file);
        $code = file_get_contents($file);
        $this->execute($code);
    }

    protected function registerCoreConstants(Engine\ConstantStore $constants) {
        $coreConstants = array(
            'caseInsensitive' => array(
                'null'  => null,
                'true'  => true,
                'false' => false,
            ),
            'caseSensitive' => array(
                'PHP_SAPI' => 'cli',
                'PHP_OS' => PHP_OS,
                'E_ERROR' => E_ERROR,
            ),
        );

        foreach ($coreConstants['caseInsensitive'] as $name => $value) {
            $constants->register($name, Zval::factory($value), false);
        }
        foreach ($coreConstants['caseSensitive'] as $name => $value) {
            $constants->register($name, Zval::factory($value));
        }

    }

    protected function registerCoreFunctions(Engine\FunctionStore $functions) {
        $coreFunctions = array(
            'implode',
            'join',
            'php_uname',
            'phpversion',
            'realpath',
            'strlen',
            'var_dump',
            'zend_version',
        );

        foreach ($coreFunctions as $funcName) {
            $functions->register($funcName, new Engine\FunctionData\Internal($funcName));
        }
    }

    protected function registerCustomFunctions(Engine\FunctionStore $functions) {
        $executor = $this->executor;
        $self = $this;
        $customFunctions = array(
            'define' => function($name, $value) use ($executor) {
                $executor->getConstantStore()->register($name, Zval::ptrFactory($value));
            },
            'error_reporting' => function($level = 0) {
                return 0;
            },
            'get_cfg_var' => function($var = null) {
                if ($var) {
                    return null;
                } else {
                    return array();
                }
            },
            'get_loaded_extensions' => function() {
                return array();
            },
            'function_exists' => function($funcName) use ($executor) {
                return $executor->getFunctionStore()->exists($funcName);
            },
        );

        foreach ($customFunctions as $funcName => $callback) {
            $functions->register($funcName, new Engine\FunctionData\Internal($callback));
        }
    }
}