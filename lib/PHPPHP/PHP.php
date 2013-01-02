<?php

namespace PHPPHP;

use PHPPHP\Engine\Zval;

class PHP {

    protected $executor;

    public function __construct() {
        $functions = new Engine\FunctionStore;
        $constants = new Engine\ConstantStore;

        $this->executor = new Engine\Executor($functions, $constants);
        $this->executor->setOutput(new Engine\Output\Std($this->executor));

        $this->executor->registerExtension(new Engine\CoreExtension);
    }

    public function execute($code) {
        $opCodes = $this->executor->compile($code);
        $retval = $this->executor->execute($opCodes);
        if ($retval) {
            return $retval->getValue();
        }
        $this->executor->getOutput()->finish();
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