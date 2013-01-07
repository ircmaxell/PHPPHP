<?php

namespace PHPPHP\Engine;

use PHPPHP\Engine\Objects\ClassEntry;

class ClassStore {
    /** @var ClassEntry[] */
    protected $classes = array();

    public function register(ClassEntry $ce) {
        $lcname = strtolower($ce->getName());
        if (isset($this->classes[$lcname])) {
            throw new \RuntimeException(sprintf("Class %s already defined", $ce->getName()));
        }
        $this->classes[$lcname] = $ce;
    }

    public function exists($name) {
        return isset($this->classes[strtolower($name)]);
    }

    public function get($name) {
        $name = strtolower($name);
        if (!isset($this->classes[$name])) {
            throw new \RuntimeException(sprintf('Undefined class %s', $name));
        }

        return $this->classes[$name];
    }

    public function getNames() {
        return array_keys($this->classes);
    }
}
