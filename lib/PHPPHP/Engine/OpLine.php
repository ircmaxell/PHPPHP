<?php

namespace PHPPHP\Engine;

class OpLine {

    public $handler;
    public $op1;
    public $op2;
    public $result;

    public function __construct(OpCode $handler = null, $op1 = null, $op2 = null, $result = null) {
        $this->handler = $handler;
        $this->op1 = $op1;
        $this->op2 = $op2;
        $this->result = $result;
    }
}