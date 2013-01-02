<?php

namespace PHPPHP\Engine;

class ConstantStore {
    /** @var Constant[] */
    protected $constants = array();

    public function register($name, Zval\Value $value, $isCaseSensitive = true) {
        if ($this->exists($name)) {
            throw new \RuntimeException("Constant \"$name\" already exists");
        }

        $const = new Constant($name, $value, $isCaseSensitive);
        if ($isCaseSensitive) {
            $this->constants[$name] = $const;
        } else {
            $this->constants[strtolower($name)] = $const;
        }
    }

    public function exists($name) {
        if (isset($this->constants[$name])) {
            return true;
        }

        $lowerName = strtolower($name);
        if (!isset($this->constants[$lowerName])) {
            return false;
        }

        return !$this->constants[$lowerName]->isCaseSensitive();
    }

    public function get($name) {
        if (isset($this->constants[$name])) {
            return $this->constants[$name]->getValue();
        }

        $lowerName = strtolower($name);
        if (isset($this->constants[$lowerName])) {
            $const = $this->constants[$lowerName];

            if (!$const->isCaseSensitive()) {
                return $const->getValue();
            }
        }

        throw new \RuntimeException("Constant \"$name\" does not exist");
    }
}