<?php

namespace PHPPHP\Engine;

class OpArray implements \ArrayAccess, \IteratorAggregate {
    /** @var OpLine[] */
    protected $opLines = array();
    protected $numOps = 0;

    public function offsetGet($offset) {
        return $this->opLines[$offset];
    }

    public function offsetSet($offset, $value) {
        if (null === $offset) {
            $this->opLines[$this->numOps++] = $value;
        } else {
            throw new \Exception('Can only append to an op array');
            //$this->opLines[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return $offset < $this->numOps;
    }

    public function offsetUnset($offset) {
        //unset($this->opLines[$offset]);
        throw new \Exception('Can not unset elements from an op array');
    }

    public function getIterator() {
        return new \ArrayIterator($this->opLines);
    }

    public function getNextOffset() {
        return $this->numOps;
    }
}