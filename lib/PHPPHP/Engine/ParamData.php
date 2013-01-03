<?php

namespace PHPPHP\Engine;

class ParamData {

    public $name;
    public $isOptional = false;
    public $isRef = false;
    public $type = null;


    public function __construct($name, $isRef = false, $type = null, $isOptional = false) {
        $this->name = $name;
        $this->isRef = $isRef;
        $this->type = $type;
        $this->isOptional = $isOptional;
    }
}