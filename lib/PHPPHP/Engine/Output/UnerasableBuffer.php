<?php

namespace PHPPHP\Engine\Output;

use PHPPHP\Engine\Zval;

class UnerasableBuffer extends Buffer {

    public function flush($force = false) {
        if (!$force) {
            if ($this->callback) {
                $this->buffer = $this->callCallback($this->buffer, $this->mode);
                $this->mode = 0;
            }
            throw new \LogicException('Unflushable Buffer');
        } else {
            parent::flush($force);
        }
    }

}