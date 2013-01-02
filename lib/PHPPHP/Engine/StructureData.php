<?php

namespace PHPPHP\Engine;

class StructureData {

    public $startOp;
    public $endOp;

    public function __construct($startOp = null, OpLines\StatementStackPop $endOp = null) {
        $this->startOp = $startOp;
        $this->endOp = $endOp;
    }
    
}