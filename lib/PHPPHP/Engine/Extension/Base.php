<?php

namespace PHPPHP\Engine\Extension;

use PHPPHP\Engine;

abstract class Base implements \PHPPHP\Engine\Extension {

    protected $name = '';
    protected $namespace = '';

    public function register(\PHPPHP\Engine\Executor $executor) {
        $functionStore = $executor->getFunctionStore();
        foreach ($this->getFunctions() as $name => $functionData) {
            $functionStore->register($name, $functionData);
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

    protected function getFunctions() {
        return array();
    }

    protected function getConstants() {
        return array();
    }

    protected function getClasses() {
        return array();
    }
}
