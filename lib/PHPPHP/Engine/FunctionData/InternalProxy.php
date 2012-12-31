<?php

namespace PHPPHP\Engine\FunctionData;

use PHPPHP\Engine;

class InternalProxy implements Engine\FunctionData {
    protected $callback;

    public function __construct($callback) {
        $this->callback = $callback;
    }

    public function execute(Engine\Executor $executor, array $args, Engine\ZvalPtr $return) {
        $rawArgs = $this->compileArguments($args);
        $ret = call_user_func_array($this->callback, $rawArgs);
        $return->zval->value = $ret;
        $return->zval->rebuildType();
    }

    public function compileArguments(array $args) {
        $self = $this;
        return array_map(function($value) use ($self) { 
            if ($value->type == Engine\Zval::IS_ARRAY) {
                return $self->compileArguments($value->value);
            }
            return $value->value; 
        }, $args);
    }
}