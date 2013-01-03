<?php

namespace PHPPHP\Engine;

class FunctionStore {
    /** @var FunctionData[] */
    protected $functions = array();

    public function alias($newName, $existingName) {
        $this->register($newName, $this->get($existingName));
    }

    public function register($name, FunctionData $func) {
        $name = strtolower($name);
        if (isset($this->functions[$name])) {
            throw new \RuntimeException("Function $name already defined");
        }

        $this->functions[$name] = $func;
    }

    public function exists($name) {
        return isset($this->functions[strtolower($name)]);
    }

    public function get($name) {
        $name = strtolower($name);
        if (!isset($this->functions[$name])) {
            throw new \RuntimeException(sprintf('Call to undefined function %s', $name));
        }

        return $this->functions[$name];
    }
    
    public function getName(FunctionData $func) {
        foreach ($this->functions as $name => $test) {
            if ($test === $func) {
                return $name;
            }
        }
        return '';
    }
}