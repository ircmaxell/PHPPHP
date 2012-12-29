<?php

namespace PHPPHP;

class PHP {
    
    protected $executor;
    
    
    public function __construct() {
        
        $this->executor = new Engine\Executor;
        $funcs = new Engine\Functions;
        $funcs->register($this->executor);
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
    
}