<?php

namespace PHPPHP\Engine;

class FunctionCall {
    protected $executor;
    protected $function;
    protected $ci;

    public function __construct(Executor $executor, FunctionData $function, Objects\ClassInstance $ci = null) {
        $this->function = $function;
        $this->ci = $ci;
        $this->executor = $executor;
    }

    public function getName() {
        if ($this->ci) {
            return $this->ci->getClassEntry()->getMethodStore()->getName($this->function);
        } else {
            return $this->executor->getFunctionStore()->getName($this->function);
        }
    }

    public function execute(array $args, \PHPPHP\Engine\Zval $result) {
        $this->function->execute($this->executor, $args, $result, $this->ci);
    }

    public function getFunction() {
        return $this->function;
    }

    public function getClassInstance() {
        return $this->ci;
    }


}