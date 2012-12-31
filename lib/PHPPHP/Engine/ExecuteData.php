<?php

namespace PHPPHP\Engine;

class ExecuteData {
    public $executor;
    public $function;
    public $opArray = array();
    public $opLine;
    public $parent;
    public $returnValue;
    public $symbolTable = array();
    protected $opPosition = 0;

    public function __construct(Executor $executor, array $opArray) {
        $this->executor = $executor;
        $this->opArray = $opArray;
        $this->opLine = $opArray[0];
        $this->returnValue = Zval::ptrFactory();
    }

    public function fetchVariable($name) {
        if (!isset($this->symbolTable[$name])) {
            $this->symbolTable[$name] = Zval::ptrFactory();
        }
        return $this->symbolTable[$name];
    }

    public function jump($position) {
        $this->opPosition = $position;
        if (!isset($this->opArray[$this->opPosition])) {
            $this->opLine = false;
            return;
        }
        $this->opLine = $this->opArray[$this->opPosition];
    }

    public function jumpTo(OpLine $opLine) {
        foreach ($this->opArray as $key => $value) {
            if ($opLine === $value && spl_object_hash($opLine) === spl_object_hash($value)) {
                $this->jump($key);
                return;
            }
        }
        throw new \Exception('Invalid Jump');
    }

    public function nextOp() {
        $this->jump($this->opPosition + 1);
    }

}