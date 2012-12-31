<?php

namespace PHPPHP\Engine;

class ZvalPtr extends Zval {

    public $iterator;
    public $zval;

    public function __construct(Zval $zval) {
        $this->zval = $zval;
    }

    public function __get($var) {
        return $this->zval->$var;
    }

    public function __set($var, $value) {
        return $this->zval->$var = $value;
    }

    public function __call($method, $args) {
        return call_user_func_array(array($this->zval, $method), $args);
    }

}