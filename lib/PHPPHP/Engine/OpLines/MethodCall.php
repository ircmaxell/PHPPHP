<?php

namespace PHPPHP\Engine\OpLines;

class MethodCall extends \PHPPHP\Engine\OpLine {

    private $objectOp;

    public function setObjectOp($objectOp) {
        $this->objectOp = $objectOp;
    }

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $object = $this->objectOp->toObject($data);
        $methodName = $this->op1->toString();
        $args = $this->op2->toArray();
        $object->callMethod($data, $methodName, $args, $this->result);
        $data->nextOp();
    }
}
