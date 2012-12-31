<?php

namespace PHPPHP\Engine\FunctionData;

use PHPPHP\Engine;

class User implements Engine\FunctionData {
    protected $opLines;
    protected $params;

    public $staticContext = array();

    public function __construct(array $opLines, array $params) {
        $this->opLines = $opLines;
        $this->params = $params;
    }

    public function execute(Engine\Executor $executor, array $args, Engine\ZvalPtr $return) {
        $scope = array();
        foreach ($this->staticContext as $key => $value) {
            $scope[$key] = $value;
        }
        foreach ($this->params as $key => $param) {
            $arg = isset($args[$key]) ? $args[$key] : $param->default;
            if ($param->isRef) {
                $scope[$param->name] = Engine\Zval::ptrFactory($arg->zval);
            } else {
                $scope[$param->name] = Engine\Zval::ptrFactory(clone $arg->zval);
            }
        }
        $executor->execute($this->opLines, $scope, $this, $args);
    }

}