<?php

namespace PHPPHP\Engine;

abstract class OpLine {
    public $op1;
    public $op2;
    public $result;
    public $lineno= 0;

    public function __construct($startLine, $op1 = null, $op2 = null, $result = null) {
        $this->op1       = $op1;
        $this->op2       = $op2;
        $this->result    = $result;
        if (!is_int($startLine)) {
            throw new \LogicException('Expecting int');
        }
        $this->lineno    = (int) $startLine;
    }

    public function getName() {
        return substr(get_class($this), strlen(__NAMESPACE__) + strlen('\OpLines\\'));
    }

    abstract public function execute(\PHPPHP\Engine\ExecuteData $data);
}