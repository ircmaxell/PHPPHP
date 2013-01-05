<?php

namespace PHPPHP\Engine\Zval;

use PHPPHP\Engine\Zval;

class Variable extends Zval {

    protected $name;
    protected $class;
    protected $zval;
    protected $executor;

    public function __construct(Zval $name, Zval $class = null) {
        $this->name = $name;
        $this->class = $class;
    }

    public function __call($method, $args) {
        $this->fetch();
        return call_user_func_array(array($this->zval, $method), $args);
    }

    public function &getArray() {
        $this->fetch();
        $ret = &$this->zval->getArray();
        return $ret;
    }

    protected function fetch() {
        $varName = $this->name->toString();
        if ($this->class) {
            if ($this->class->isString()) {
                $ci = $this->executor->getClassStore()->get($this->class->getValue());
            } else if ($this->class->isObject()) {
                $ci = $this->class->getValue()->getClassEntry();
            } else {
                throw new \RuntimeException('Class name must be a valid object or a string');
            }
            $this->zval = $ci->fetchStaticVariable($varName);
        } else if ($varName == 'this') {
            $this->zval = Zval::lockedPtrFactory($this->executor->getCurrent()->ci);
        } else {
            $this->zval = $this->executor->getCurrent()->fetchVariable($varName);
        }
    }

    public function setExecutor(\PHPPHP\Engine\Executor $executor) {
        $this->executor = $executor;
    }

    public function getName() {
        return $this->name->toString();
    }
}
