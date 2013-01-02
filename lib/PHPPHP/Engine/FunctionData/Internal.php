<?php

namespace PHPPHP\Engine\FunctionData;

use PHPPHP\Engine;

class Internal implements Engine\FunctionData {
    protected $callback;

    public function __construct($callback) {
        $this->callback = $callback;
    }

    public function execute(Engine\Executor $executor, \CanisM\HashTable\HashTable $args, Engine\Zval\Ptr $return) {
        $args = $this->compileArguments($args);
        $ret = call_user_func_array($this->callback, array($executor, $args, $return));
    }

    public function compileArguments(\CanisM\HashTable\HashTable $args) {
        $ret = array();
        foreach ($args as $key => $value) {
            $ret[$key] = $value;
        }
        return $ret;
    }

}