<?php

namespace PHPPHP\Engine;

class FunctionCall {
    protected $function;
    protected $ci;

    public function __construct(FunctionData $function, Objects\ClassInstance $ci = null) {
        $this->function = $function;
        $this->ci = $ci;
    }

    public function getFunction() {
        return $this->function;
    }

    public function getClassInstance() {
        return $this->ci;
    }


}