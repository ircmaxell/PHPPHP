<?php

namespace PHPPHP\Engine\FunctionData;

use PHPPHP\Engine;

class User implements Engine\FunctionData {
    protected $opArray;
    protected $byRef = false;
    protected $params = array();

    public function __construct(Engine\OpArray $opArray, $byRef = false, array $params) {
        $this->opArray = $opArray;
        $this->byRef = $byRef;
        $this->params = $params;
    }

    public function execute(Engine\Executor $executor, array $args, Engine\Zval\Ptr $return, \PHPPHP\Engine\Objects\ClassInstance $ci = null) {
        $scope = array();
        if (!$args) {
            $args = array();
        }
        if ($this->byRef) {
            $return->makeRef();
        }
        $executor->execute($this->opArray, $scope, $this, $args, $return, $ci);
    }

    public function isByRef() {
        return $this->byRef;
    }

    public function isArgByRef($n) {
        return false;
    }

    public function getParam($n) {
        return isset($this->params[$n]) ? $this->params[$n] : false;
    }
}