<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine;
use PHPPHP\Engine\Zval;
use PHPPHP\Engine\Objects;

class InitFCallByName extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $ci = $this->op1;
        $func = $this->op2;
        if ($ci) {
            if ($ci->isObject()) {
                $ci = $ci->getValue();
                $functionData = $ci->getClassEntry()->getMethodStore()->get($func->toString());
                $call = new Engine\FunctionCall($data->executor, $functionData, $ci);
            } else {
                throw new \RuntimeException(sprintf('Call to a member function %s() on a non-object', $func->toString()));
            }
        } else {
            // $functionData = $data->executor->getFunctionStore()->get($func->toString());
            $call = $this->getCall($data, $func);
        }

        $data->executor->executorGlobals->call = $call;

        $data->nextOp();
    }

    public function getCall(\PHPPHP\Engine\ExecuteData $data, $func) {
        if ($func instanceof Zval) {
            $func = $func->getValue();
        }
        if (is_string($func)) {
            $functionData = $data->executor->getFunctionStore()->get($func);
            return new Engine\FunctionCall($data->executor, $functionData, null);
        } elseif (is_array($func)) {
            $class = $func[0];
            $method = $func[1];
            if ($class instanceof Zval) {
                $class = $class->getValue();
            }
            if ($method instanceof Zval) {
                $method = $method->getValue();
            }
            if ($class instanceof Objects\ClassInstance) {
                // return function(Executor $executor, array $args = array(), Zval $return = null) use ($class, $method) {
                //     $class->callMethod($executor->getCurrent(), (string) $method, $args, $return);
                // };
                $functionData = $class->getClassEntry()->findMethod((string) $method);
                return new Engine\FunctionCall($data->executor, $functionData, $class);
            }
            if (is_string($class)) {
                $class = $this->classStore->get($class);
            }
            if ($class instanceof Objects\ClassEntry) {
                // Static method!
                // return function(Executor $executor, array $args = array(), Zval $return = null) use ($class, $method) {
                //     $class->callMethod($executor->getCurrent(), null, (string) $method, $args, $return);
                // };
                $functionData = $class->getClassEntry()->findMethod((string) $method);
                return new Engine\FunctionCall($data->executor, $functionData, null, $class);
            }
        } elseif ($func instanceof Objects\ClassInstance && $func->getClassEntry()->hasMethod('__invoke')) {
            // return function(Executor $executor, array $args = array(), Zval $return = null) use ($class) {
            //     $class->callMethod($executor->getCurrent(), '__invoke', $args, $return);
            // };
            $functionData = $func->getClassEntry()->findMethod('__invoke');
            return new Engine\FunctionCall($data->executor, $functionData, $func);
        } elseif ($func instanceof FunctionData) {
            // return function(Executor $executor, array $args = array(), Zval $return = null) use ($func) {
            //     return $func->execute($executor, $args, $return);
            // };
            return new Engine\FunctionCall($data->executor, $func);
        }

        throw new \RuntimeException('Invalid Callback Specified');
    }

}