<?php

namespace PHPPHP\Engine;

class BreakContinueInfo {
    public $continueOp;
    public $breakOp;
    public $parentPos;

    public function __construct($parentPos) {
        $this->parentPos = $parentPos;
    }
}