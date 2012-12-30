<?php

namespace PHPPHP\Engine;

class Constant {
    protected $name;
    protected $value;
    protected $isCaseSensitive;

    public function __construct($name, Zval $value, $isCaseSensitive) {
        $this->name = $name;
        $this->value = $value;
        $this->isCaseSensitive = $isCaseSensitive;
    }

    public function getName() { return $this->name; }
    public function getValue() { return $this->value; }
    public function isCaseSensitive() { return $this->isCaseSensitive; }
}