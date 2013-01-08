<?php

namespace PHPPHP\Engine\Output;

use PHPPHP\Engine\Zval;

class Buffer extends \PHPPHP\Engine\Output {

    protected $buffer = '';
    protected $callback = null;
    protected $mode = PHP_OUTPUT_HANDLER_START;

    public function __construct(\PHPPHP\Engine\Executor $executor, $callback = null) {
        parent::__construct($executor);
        if ($callback && !is_callable($callback)) {
            throw new \LogicException('Non-callable callback provided');
        }
        $this->callback = $callback;
    }

    public function getCallback() {
        return $this->callback;
    }
    
    public function clean() {
        $this->mode = 0;
        $this->buffer = '';
    }

    public function end() {
        $this->executor->setOutput($this->parent);
    }

    public function endFlush($force = false) {
        $this->mode |= PHP_OUTPUT_HANDLER_END;
        $this->flush($force);
        $this->end();
    }

    public function write($data) {
        $this->buffer .= $data;
    }

    public function finish($force = true) {
        $this->endFlush($force);
        $this->parent->finish($force);
    }

    public function flush($force = false) {
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
            $this->buffer = '';
            $current = $this->executor->getOutput();
            $ro = new ReadOnly($this->executor);
            $this->executor->setOutput($ro);
            try {
                $ret = Zval::ptrFactory();
                $args = array(
                    Zval::ptrFactory($data),
                    Zval::ptrFactory($mode),
                );
                $cb = $this->callback;
                $cb($this->executor, $args, $ret);
            } catch (\PHPPHP\Engine\ErrorOccurredException $e) {
                // Restore error handler first!
                $this->executor->setOutput($current);
                throw $e;
            }
            $this->executor->setOutput($current);
            if ($ret->isBool() || $ret->toBool() == false) {
                return $data;
            } else {
                return $ret->toString();
            }
        }
        return $data;
    }

}