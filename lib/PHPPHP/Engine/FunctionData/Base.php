<?php

namespace PHPPHP\Engine\FunctionData;

use PHPPHP\Engine;

abstract class Base implements Engine\FunctionData {

    protected $byRef = false;
    protected $params = array();

    public function isByRef() {
        return $this->byRef;
    }

    public function isArgByRef($n) {
        $param = $this->getParam($n);
        return $param && $param->isRef;
    }

    public function getParam($n) {
        return isset($this->params[$n]) ? $this->params[$n] : false;
    }
}