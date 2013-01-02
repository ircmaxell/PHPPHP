<?php

namespace PHPPHP\Engine;

abstract class Output {

    protected $executor;
    protected $parent;

    public function __construct(\PHPPHP\Engine\Executor $executor) {
        $this->executor = $executor;
        $this->parent = $executor->getOutput();
    }

    public function finish() {
        if ($this->parent) {
            $this->parent->finish();
        }
    }

    public function getParent() {
        return $this->parent;
    }

    abstract public function write($data);

}