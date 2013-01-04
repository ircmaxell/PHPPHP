<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

abstract class BinaryAssign extends \PHPPHP\Engine\OpLine {

    public $dim;
    public $property;

    protected function getValue() {

        if ($this->property) {
            if (!$this->op1->isObject()) {
                throw new \RuntimeException('Attempt to assign property of non-object');
            }
            return $this->op1->getValue()->getProperty($this->property->toString())->getValue();

        } else if ($this->dim) {
            $array = $this->op1->getArray();
            return $array[$this->dim->toString()]->getValue();

        } else {
            return $this->op1->getValue();
        }
    }

    protected function setValue($value) {

        $zval = Zval::factory($value);

        if ($this->property) {
            $this->op1->getValue()->setProperty($this->property->toString(), $zval);

        } else if ($this->dim) {
            $array =& $this->op1->getArray();
            $key = $this->dim->toString();
            if (isset($array[$key])) {
                $array[$key]->setValue($zval);
            } else {
                $array[$key] = Zval::ptrFactory($zval);
            }

        } else {
            $this->op1->setValue($zval);
        }

        if ($this->result) {
            $this->result->setValue($zval);
        }
    }
}
