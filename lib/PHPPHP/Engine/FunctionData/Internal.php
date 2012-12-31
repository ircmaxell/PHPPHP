<?php

namespace PHPPHP\Engine\FunctionData;

use PHPPHP\Engine;

class Internal implements Engine\FunctionData {
    protected $callback;

    public function __construct($callback) {
        $this->callback = $callback;
    }

    public function execute(Engine\Executor $executor, array $args, Engine\ZvalPtr $return) {
        $rawArgs = array_map(function($value) { return $value->value; }, $args);
        $ret = call_user_func_array($this->callback, $rawArgs);
        $return->zval->value = $ret;
        $return->zval->rebuildType();
    }
}