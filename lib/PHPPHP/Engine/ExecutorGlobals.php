<?php

namespace PHPPHP\Engine;

class ExecutorGlobals {

    public $call = null;

    public $cwd = '';

    /**
     * The global symbol table
     * @var array The global symbol table for variables
     */
    public $symbolTable = array();
    
    public $superGlobals = array();
    
}