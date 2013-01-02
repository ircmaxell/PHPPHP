<?php

namespace PHPPHP\Engine\Extension;

use PHPPHP\Engine;

abstract class Base implements \PHPPHP\Engine\Extension {

    protected $name = '';
    protected $namespace = '';

    public function register(\PHPPHP\Engine\Executor $executor) {
        $functionStore = $executor->getFunctionStore();
        foreach ($this->findFunctions() as $name => $callback) {
            $functionStore->register($name, new Engine\FunctionData\Internal($callback));
        }
        $constantStore = $executor->getConstantStore();
        foreach ($this->getConstants() as $name => $value) {
            $constantStore->register($name, Engine\Zval::factory($value));
        }
        $classStore = $executor->getClassStore();
        foreach ($this->getClasses() as $ce) {
            $classStore->register($ce);
        }
    }

    public function getName() {
        return $this->name;
    }

    protected function findFunctions() {
        $this->loadFunctions();
        $return = array();
        $funcs = get_defined_functions();
        $ns = strtolower($this->namespace . '\PHP_');
        foreach ($funcs['user'] as $func) {
            if (strpos($func, $ns) === 0) {
                $return[substr($func, strlen($ns))] = $func;
            }
        }
        return $return;
    }

    abstract protected function loadFunctions();

    protected function getConstants() {
        return array();
    }

    protected function getClasses() {
        return array();
    }
}
