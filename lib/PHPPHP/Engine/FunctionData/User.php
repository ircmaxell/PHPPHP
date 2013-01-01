<?php

namespace PHPPHP\Engine\FunctionData;

use PHPPHP\Engine;

class User implements Engine\FunctionData {
    protected $opArray;

    public $staticContext = array();

    public function __construct(Engine\OpArray $opArray) {
        $this->opArray = $opArray;
    }

    public function execute(Engine\Executor $executor, array $args, Engine\ZvalPtr $return) {
        $scope = array();
        foreach ($this->staticContext as $key => $value) {
            $scope[$key] = $value;
        }
        $executor->execute($this->opArray, $scope, $this, $args);
    }

}