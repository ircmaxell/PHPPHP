<?php

namespace PHPPHP\Engine\FunctionData;

use PHPPHP\Engine;

class User implements Engine\FunctionData {
    protected $opArray;

    public $staticContext = array();

    public function __construct(Engine\OpArray $opArray) {
        $this->opArray = $opArray;
    }

    public function execute(Engine\Executor $executor, \CanisM\HashTable\HashTable $args, Engine\Zval\Ptr $return) {
        $scope = array();
        foreach ($this->staticContext as $key => $value) {
            $scope[$key] = $value;
        }
        if ($args->count()) {
            $args = $args->toArray();
        } else {
            $args = array();
        }
        $executor->execute($this->opArray, $scope, $this, $args, $return);
    }

}