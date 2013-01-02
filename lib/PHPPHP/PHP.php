<?php

namespace PHPPHP;

use PHPPHP\Engine\Zval;

class PHP {

    protected $executor;

    public function __construct() {
        $functions = new Engine\FunctionStore;
        $constants = new Engine\ConstantStore;
        $classes = new Engine\ClassStore;

        $this->executor = new Engine\Executor($functions, $constants, $classes);

        $this->executor->registerExtension(new Engine\CoreExtension);
    }

    public function execute($code) {
        $opCodes = $this->executor->compile($code);
        $retval = $this->executor->execute($opCodes);
        if ($retval) {
            return $retval->getValue();
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


}
