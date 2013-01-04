<?php

namespace PHPPHP\Engine\Output;

use PHPPHP\Engine\Zval;

class Buffer extends \PHPPHP\Engine\Output {

    protected $buffer = '';
    protected $callback = null;
    protected $mode = PHP_OUTPUT_HANDLER_START;

    public function __construct(\PHPPHP\Engine\Executor $executor, callable $callback = null) {
        parent::__construct($executor);
        $this->callback = $callback;
    }

    public function clean() {
        $this->mode = 0;
        $this->buffer = '';
    }

    public function endFlush() {
        $this->mode |= PHP_OUTPUT_HANDLER_END;
        $this->flush();
    }

    public function write($data) {
        $this->buffer .= $data;
    }

    public function finish() {
        $this->endFlush();
        $this->parent->finish();
    }

    public function flush() {
        if ($this->callback) {
            $this->parent->write($this->callCallback($this->buffer, $this->mode));
            $this->mode = 0;
        } else {
            $this->parent->write($this->buffer);
        }
        $this->buffer = '';
    }

    public function getBuffer() {
        return $this->buffer;
    }

    public function setBuffer($data) {
        $this->buffer = $data;
    }

    protected function callCallback($data, $mode) {
        if ($this->callback) {
            $ret = Zval::ptrFactory();
            $args = array(
                Zval::ptrFactory($data),
                Zval::ptrFactory($mode),
            );
            $cb = $this->callback;
            $cb($this->executor, $args, $ret);
            if ($ret->isBool() || $ret->toBool() == false) {
                return $data;
            } else {
                return $ret->toString();
            }
        }
        return $data;
    }

}