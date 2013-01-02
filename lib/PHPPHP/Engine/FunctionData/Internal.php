<?php

namespace PHPPHP\Engine\FunctionData;

use PHPPHP\Engine;

class Internal implements Engine\FunctionData {
    protected $callback;

    public function __construct($callback) {
        $this->callback = $callback;
    }

    public function execute(Engine\Executor $executor, array $args, Engine\Zval\Ptr $return, \PHPPHP\Engine\Objects\ClassInstance $ci = null) {
        $ret = call_user_func_array($this->callback, array($executor, $args, $return, $ci));
    }

}