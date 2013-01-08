<?php

namespace PHPPHP\Engine\Output;

use PHPPHP\Engine\Zval;

class ReadOnly extends Buffer {

    public function __construct(\PHPPHP\Engine\Executor $executor) {
        \PHPPHP\Engine\Output::__construct($executor);
    }

    public function clean() {
        $this->executor->raiseError(E_ERROR, 'Cannot use output buffering in output buffering display handlers');
    }

    public function endFlush($force = false) {
        $this->executor->raiseError(E_ERROR, 'Cannot use output buffering in output buffering display handlers');
    }

    public function write($data, $isError = false) {
        if ($isError) {
            $this->parent->write($data);
        } else {
            $this->executor->raiseError(E_ERROR, 'Cannot use output buffering in output buffering display handlers');
        }
    }

    public function finish($force = false) {
        $this->executor->raiseError(E_ERROR, 'Cannot use output buffering in output buffering display handlers');
    }

    public function flush($force = false) {
        $this->executor->raiseError(E_ERROR, 'Cannot use output buffering in output buffering display handlers');
    }

    public function getBuffer() {
        $this->executor->raiseError(E_ERROR, 'Cannot use output buffering in output buffering display handlers');
    }

    public function setBuffer($data) {
        $this->executor->raiseError(E_ERROR, 'Cannot use output buffering in output buffering display handlers');
    }

}