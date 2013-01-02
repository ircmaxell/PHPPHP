<?php

namespace PHPPHP\Engine\Zval;

use CanisM\HashTable\HashTable;
use PHPPHP\Engine\Zval;

class Value extends Zval {

    protected $value;
    protected $refcount = 1;
    protected $isRef = false;

    protected $dtorFunc;

    public function __construct($value, $dtorFunc = null) {
        $this->setValue($value);
        $this->dtorFunc = $dtorFunc;
    }

    public function __clone() {
        $this->refcount = 1;
        $this->isRef = false;
    }

    public function getRefcount() {
        return $this->refcount;
    }

    public function addRef() {
        $this->refcount++;
    }

    public function delRef() {
        $this->refcount--;
        if ($this->refcount <= 0 && $this->dtorFunc) {
            call_user_func($this->dtorFunc, $this);
        }
    }

    public function isRef() {
        return $this->isRef;
    }

    public function makeRef() {
        $this->isRef = true;
    }

    public function getValue() {
        return $this->value;
    }

    public function getIterator() {
        if ($this->value instanceof HashTable) {
            // Fix bug with single iterator for HT.
            $ret = clone $this->value;
            $ret->rewind();
            return $ret;
        }
        throw new \LogicException('No Default Iterator Provided');
    }

    public function getHashTable() {
        if ($this->value instanceof HashTable) {
            return $this->value;
        }
        throw new \LogicException('Getting hash table of invalid zval');
    }

    public function makePrintable() {
        if (!$this->isString()) {
            return $this->castTo('string');
        }
        return $this;
    }

    public function castTo($type) {
        switch ($type) {
            case 'array':
                return static::factory($this->toArray());
            case 'bool':
                return static::factory($this->toBool());
            case 'double':
                return static::factory($this->toDouble());
            case 'long':
                return static::factory($this->toLong());
            case 'string':
                return static::factory($this->toString());
            default:
                throw new \LogicException('Unknown Type: ' . $type);
        }
    }

    public function isArray() {
        return $this->value instanceof HashTable;
    }

    public function isBool() {
        return is_bool($this->value);
    }
    public function isDouble() {
        return is_double($this->value);
    }

    public function isLong() {
        return is_int($this->value);
    }

    public function isNull() {
        return is_null($this->value);
    }

    public function isString() {
        return is_string($this->value);
    }

    public function toArray() {
        if (!$this->isArray()) {
            $ht = new HashTable;
            if (!$this->isNull()) {
                $ht->append($this->value);
            }
            return $ht;
        }
        return clone $this->value;
    }

    public function toBool() {
        return (bool) $this->value;
    }

    public function toDouble() {
        return (double) $this->value;
    }

    public function toLong() {
        return (int) $this->value;
    }

    public function toString() {
        return (string) $this->value;
    }

    public function setValue($value) {
        if ($value instanceof self) {
            $value = $value->value;
        } elseif ($value instanceof Zval) {
            $value = $value->getValue();
        }

        if (is_array($value)) {
            $ht = new HashTable;
            foreach ($value as $key => $val) {
                $ht->store($key, static::factory($val));
            }
            $value = $ht;
        }
        $this->value = $value;
    }

}