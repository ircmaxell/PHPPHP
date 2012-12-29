<?php

namespace PHPPHP\Engine;

class Executor {
    const DO_RETURN = 1;
    
    protected $stack = array();
    protected $current;
    protected $globalScope = array();
    protected $functions = array();

    public function execute(array $opLines, array $symbolTable = array()) {
        $scope = new ExecuteData($this, $opLines);
        if ($this->current) {
            $scope->parent = $this->current;
        }
        $this->stack[] = $scope;
        $this->current = $scope;
        if ($symbolTable) {
            $scope->symbolTable = $symbolTable;
        }
        
        while ($scope->opLine) {
            $ret = $scope->opLine->handler->execute($scope);
            switch ($ret) {
                case self::DO_RETURN:
                    array_pop($this->stack);
                    if ($scope->parent) {
                        $scope->parent->opLine->result->zval = $scope->returnValue->zval;
                    }
                    $this->current = end($this->stack);
                    return;
            }
        }
    }
    
    public function addFunction($name, FunctionData $func) {
        $name = strtolower($name);
        if (isset($this->functions[$name])) {
            throw new \RuntimeException('Duplication Function');
        }
        $this->functions[$name] = $func;
    }
    
    public function getFunction($name) {
        $name = strtolower($name);
        if (isset($this->functions[$name])) {
            return $this->functions[$name];
        }
        throw new \RuntimeException('Call to undefined function');
    }

}
