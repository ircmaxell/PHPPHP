<?php

namespace PHPPHP\Engine\FunctionData;

use PHPPHP\Engine;

class User implements Engine\FunctionData {
    protected $opLines;
    protected $params;

    public function __construct(array $opLines, array $params) {
        $this->opLines = $opLines;
        $this->params = $params;
    }

    public function execute(Engine\Executor $executor, array $args, Engine\ZvalPtr $return) {
        $rawArgs = array();
        foreach ($this->params as $key => $param) {
            $arg = isset($args[$key]) ? $args[$key] : $param->default;
            $rawArgs[$param->name] = Engine\Zval::ptrFactory(clone $arg->zval);
        }
        $executor->execute($this->opLines, $rawArgs);
    }
}