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
        $this->executor->setOutput(new Engine\Output\Std($this->executor));

        $this->executor->registerExtension(new Engine\CoreExtension);
    }

    public function execute($code) {
        $opCodes = $this->executor->compile($code, 'Command line code');
        return $this->executeOpLines($opCodes);
    }

    public function executeFile($file) {
        if (empty($file)) {
            throw new \RuntimException('Filename must not be emptys');
        }
        
        $opCodes = $this->executor->compileFile($file);
        return $this->executeOpLines($opCodes);
    }

    public function executeOpLines(Engine\OpArray $opCodes) {
        $retval = $this->executor->execute($opCodes);
        if ($retval) {
            return $retval->getValue();
        }
        $this->executor->getOutput()->finish();
        return null;
    }

}
