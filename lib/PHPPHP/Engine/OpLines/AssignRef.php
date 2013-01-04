<?php

namespace PHPPHP\Engine\OpLines;

class AssignRef extends \PHPPHP\Engine\OpLine {

    public $property;
    public $dim;

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $this->op2->makeRef();
        $zval = $this->op2->getZval();

        if ($this->property) {
            $this->op1->getValue()->setProperty($this->property->toString(), $zval);

        } else if ($this->dim) {
            $array =& $this->op1->getArray();
            $key = $this->property->toString();
            if (isset($array[$key])) {
                $array[$key]->forceValue($zval);
            } else {
                $array[$key] = Zval::ptrFactory($zval);
            }

        } else {
            $this->op1->forceValue($zval);
        }

        if ($this->result) {
            $this->result->setValue($zval);
        }

        $data->nextOp();
    }

}
