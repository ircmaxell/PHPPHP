<?php

namespace PHPPHP\Engine\FunctionData;

use PHPPHP\Engine;

class InternalProxy implements Engine\FunctionData {
    protected $callback;

    public function __construct($callback) {
        $this->callback = $callback;
    }

    public function execute(Engine\Executor $executor, array $args, Engine\Zval\Ptr $return = null) {
        $rawArgs = $this->compileArguments($args);
        ob_start();
        $ret = call_user_func_array($this->callback, $rawArgs);
        $out = ob_get_clean();
        if ($out) {
            $executor->getOutput()->write($out);
        }
        if ($return) {
            $return->setValue($this->compileReturn($ret));
        }
    }

    public function compileReturn($value) {
        if (is_array($value)) {
            $result = array();
            foreach ($value as $key => $item) {
                $result[$key] = $this->compileReturn($item);
            }
            return Engine\Zval::factory($result);
        } else {
            return Engine\Zval::factory($value);
        }
    }

    public function compileArguments(array $args) {
        $ret = array();
        foreach ($args as $key => $value) {
            if ($value->isArray()) {
                $ret[$key] = $this->compileArguments($value->toArray());
            } else {
                $ret[$key] = $value->getValue();
            }
        }
        return $ret;
    }
}