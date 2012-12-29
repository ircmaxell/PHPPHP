<?php

namespace PHPPHP;

class PHP {
    
    protected $executor;
    protected $parser;
    protected $compiler;
    
    public function __construct() {
        $this->parser = new Engine\Parser;
        $this->compiler = new Engine\Compiler;
        $this->executor = new Engine\Executor;
        $funcs = new Engine\Functions;
        $funcs->register($this->executor);
    }
    
    public function execute($code) {
        $ast = $this->parser->parse($code);
        $opCodes = $this->compiler->compile($ast);
        $this->executor->execute($opCodes);
    }
    
}