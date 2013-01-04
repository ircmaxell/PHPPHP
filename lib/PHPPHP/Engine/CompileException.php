<?php

namespace PHPPHP\Engine;

class CompileException extends \LogicException {
    
    protected $rawLine = 0;

    public function __construct($message, $line = -1) {
        parent::__construct($message);
        $this->rawLine = (int) $line;
    }

    public function getRawLine() {
        return $this->rawLine;
    }
}