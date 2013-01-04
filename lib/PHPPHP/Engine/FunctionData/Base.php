<?php

namespace PHPPHP\Engine\FunctionData;

use PHPPHP\Engine;

abstract class Base implements Engine\FunctionData {

    protected $name;
    protected $byRef = false;
    protected $params = array();

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function isByRef() {
        return $this->byRef;
    }

    public function isArgByRef($n) {
        $param = $this->getParam($n);
        return $param && $param->isRef;
    }

    public function getParams() {
        return $this->params;
    }

    public function getParam($n) {
        return isset($this->params[$n]) ? $this->params[$n] : false;
    }

    protected function checkParams(\PHPPHP\Engine\Executor $executor, array &$args, $checkTooMany = false) {
        $argNo = 0;
        $required = 0;
        $hasOptional = false;
        $has = count($args);
        $varargs = false;
        while ($param = $this->getParam($argNo)) {
            if (!$param->isOptional) {
                $required++;
                if (!isset($args[$argNo])) {
                    $args[$argNo] = Engine\Zval::ptrFactory();
                }
            } else {
                $hasOptional = true;
            }
            if ($param->name == '...') {
                $varargs = true;
            }
            $argNo++;
        }
        if ($required > $has) {
            $message = $this->name;
            $message .= "() expects ";
            $message .= $hasOptional ? "at least" : "exactly";
            $message .= " $required " . ($required == 1 ? "parameter" : "parameters");
            $message .= ", $has given";
            $executor->raiseError(E_WARNING, $message);
            return false;
        } elseif ($checkTooMany && !$varargs && $has > $argNo) {
            $message = $this->name;
            $message .= "() expects ";
            $message .= $hasOptional ? "at most" : "exactly";
            $message .= " $argNo " . ($argNo == 1 ? "parameter" : "parameters");
            $message .= ", $has given";
            $executor->raiseError(E_WARNING, $message);
            return false;
        }
        return true;
    }
}