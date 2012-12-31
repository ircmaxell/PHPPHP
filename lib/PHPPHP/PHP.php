<?php

namespace PHPPHP;

use PHPPHP\Engine\Zval;

class PHP {

    protected $executor;

    public function __construct() {
        $constants = new Engine\ConstantsStore;
        $constants->register('null',  Zval::factory(null),  false);
        $constants->register('true',  Zval::factory(true),  false);
        $constants->register('false', Zval::factory(false), false);
        $constants->register('PHP_SAPI', Zval::factory('cli'));
        $constants->register('PHP_OS', Zval::factory(PHP_OS));

        $this->executor = new Engine\Executor($constants);
        $funcs = new Engine\Functions;
        $funcs->register($this->executor);
    }

    public function execute($code) {
        $opCodes = $this->executor->compile($code);
        $this->executor->execute($opCodes);
    }

    public function executeFile($file) {
        if (empty($file)) {
            return;
        }
        $this->executor->executorGlobals->cwd = dirname($file);
        $code = file_get_contents($file);
        $this->execute($code);
    }

}