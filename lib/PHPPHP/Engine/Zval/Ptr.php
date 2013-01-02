<?php

namespace PHPPHP\Engine\Zval;

use PHPPHP\Engine\Zval;

class Ptr extends Zval {

    private $zval;

    public function __construct(Value $zval) {
        $this->zval = $zval;
    }

    public function __destruct() {
        if ($this->zval instanceof Value) {
            $this->zval->delRef();
        }
    }

    public function __call($method, $args) {
        return call_user_func_array(array($this->zval, $method), $args);
    }

    public function getZval() {
        $tmp = $this->zval;
        do {
            $value = $tmp;
            $tmp = $value->getZval();
        } while ($value !== $tmp);
        return $value;
    }

    public function makeRef() {
        if ($this->zval instanceof Variable) {
            return $this->zval->makeRef();
        }
        $this->separateIfNotRef();
        $this->zval->makeRef();
    }

    public function assignZval(Zval $value) {
        if ($value instanceof Ptr) {
            $value = $value->getZval();
        }
        $this->zval = $value;
    }

    public function forceValue(Zval $value) {
        $value = $value->getZval();
        $this->zval->delRef();
        $this->zval = $value;
        $this->zval->addRef();
    }

    public function setValue($value) {
        if ($value instanceof Zval) {
            $value = $value->getZval();
        }
        if ($this->zval instanceof Variable) {
            return $this->zval->setValue($value);
        }
        if ($value instanceof Zval) {
            if ($this->zval->isRef()) {
                $this->zval->setValue($value);
            } elseif ($value->isRef()) {
                $this->zval->setValue($value);
            } else {
                $this->forceValue($value);
            }
        } elseif ($value !== $this->zval->getValue()) {
            $this->separateIfNotRef();
            $this->zval->setValue($value);
        }
    }

    protected function separate() {
        if ($this->zval instanceof Variable) {
            return $this->zval->separate();
        }
        if ($this->zval->getRefcount() > 1) {
            $this->zval = clone $this->zval;
        }
        return $this;
    }

    protected function separateIfNotRef() {
        if ($this->zval instanceof Variable) {
            return $this->zval->separateIfNotRef();
        }
        if (!$this->zval->isRef()) {
            $this->separate();
        }
        return $this;
    }

    protected function &separateIfRef() {
        if ($this->zval instanceof Variable) {
            return $this->zval->separateIfRef();
        }
        if ($this->zval->isRef()) {
            $this->separate();
        }
        return $this;
    }
}