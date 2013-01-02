<?php

namespace PHPPHP\Engine\Zval;

use PHPPHP\Engine\Zval;

class Iterator extends Zval {

    protected $iterator;

    public function __construct(\Traversable $iterator = null) {
        $this->setIterator($iterator);
    }


    public function setIterator(\Traversable $iterator = null) {
        $this->iterator = $iterator;
    }
    
    public function getIterator() {
        if ($this->iterator) {
            return $this->iterator;
        }
        return new \EmptyIterator;
    }


}