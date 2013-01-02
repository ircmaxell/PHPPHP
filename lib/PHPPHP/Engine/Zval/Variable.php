<?php

namespace PHPPHP\Engine\Zval;

use PHPPHP\Engine\Zval;

class Variable extends Zval {

    protected $name;
    protected $zval;
    protected $executor;

    public function __construct(Zval $name) {
        $this->name = $name;
    }

    public function __call($method, $args) {
        $this->zval = $this->executor->getCurrent()->fetchVariable($this->name->toString());
        return call_user_func_array(array($this->zval, $method), $args);
    }

    public function setExecutor(\PHPPHP\Engine\Executor $executor) {
        $this->executor = $executor;
    }

    public function getName() {
        return $this->name->toString();
    }
}