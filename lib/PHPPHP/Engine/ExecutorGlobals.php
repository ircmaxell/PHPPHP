<?php

namespace PHPPHP\Engine;

class ExecutorGlobals {

    public $call = null;

    public $cwd = '';
    
    public $display_errors = true;
    
    public $error_reporting = -1;

    /**
     * The global symbol table
     * @var array The global symbol table for variables
     */
    public $symbolTable = array();
    
    public $superGlobals = array();

    public $timeLimit = 0;

    public $timeLimitEnd = PHP_INT_MAX;
    
}