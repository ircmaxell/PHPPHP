<?php

namespace PHPPHP\Engine;

class FunctionData {

    const IS_INTERNAL = 1;
    const IS_USER = 2;
    
    public $executor;
    public $callback = '';
    public $opLines = array();
    public $params = array();
    public $type = self::IS_USER;
    
    public function __construct(Executor $executor, $type) {
        $this->executor = $executor;
        $this->type = $type;
    }
    
    public function execute(array $args, ZvalPtr $return) {
        if ($this->type == static::IS_INTERNAL) {
            $rawArgs = array_map(function($value) { return $value->value; }, $args);
            $ret = call_user_func_array($this->callback, $rawArgs);
            $return->zval->value = $ret;
            $return->zval->rebuildType();
        } elseif ($this->type == static::IS_USER) {
            $rawArgs = array();
            foreach ($this->params as $key => $param) {
                 $arg = isset($args[$key]) ? $args[$key] : $param->default;
                 $rawArgs[$param->name] = Zval::ptrFactory(clone $arg->zval);
            }
            $this->executor->execute($this->opLines, $rawArgs);
        }
    }
    
    
}