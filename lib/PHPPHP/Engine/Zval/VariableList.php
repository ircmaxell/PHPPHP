<?php

namespace PHPPHP\Engine\Zval;

use PHPPHP\Engine\Zval;

class VariableList extends Zval {

    protected $name;
    protected $zval;
    protected $executor;
    protected $values;

    public function __construct(array $values) {
        $this->values = $values;
    }

    public function __call($method, $args) {
        throw new \Exception('Invalid Call');
        $this->zval = $this->executor->getCurrent()->fetchVariable($this->name->toString());
        return call_user_func_array(array($this->zval, $method), $args);
    }

    public function addRef() {}

    public function delRef() {}

    public function isRef() {
        return false;
    }

    public function getZval() {
        return $this;
    }

    public function setValue($value) {
        if ($value instanceof Zval) {
            if ($value->isArray()) {
                $value = $value->toArray();
            }
        }
        if (is_array($value)) {
            foreach ($this->values as $key => $val) {
                if (isset($value[$key]) && $val) {
                    $val->setValue($value[$key]);
                } elseif ($val) {
                    $val->setValue(null);
                }
            }
        }
    }

    public function setExecutor(\PHPPHP\Engine\Executor $executor) {
        $this->executor = $executor;
    }

    public function getName() {
        return $this->name->toString();
    }
}