<?php

namespace PHPPHP\Engine;

class ParamData {

    public $name;
    public $isOptional = false;
    public $isRef = false;
    public $type = null;
    public $lineno = -1;


    public function __construct($name, $isRef = false, $type = null, $isOptional = false, $lineno = -1) {
        $this->name = $name;
        $this->isRef = $isRef;
        $this->type = $type;
        $this->isOptional = $isOptional;
        $this->lineno = $lineno;
    }
}