<?php

namespace PHPPHP\Engine;

abstract class OpLine {
    public $op1;
    public $op2;
    public $result;
    public $attributes = array();

    public function __construct(\PHPParser_Node $node, $op1 = null, $op2 = null, $result = null) {
        $this->op1    = $op1;
        $this->op2    = $op2;
        $this->result = $result;
        $this->attributes['startLine']   = $node->getLine();
    }

    abstract public function execute(\PHPPHP\Engine\ExecuteData $data);
}