<?php

namespace PHPPHP\Engine;

class ZvalValue extends Zval {

    const IS_NULL = 0;
    const IS_LONG = 1;
    const IS_DOUBLE = 2;
    const IS_BOOL = 3;
    const IS_ARRAY = 4;
    const IS_OBJECT = 5;
    const IS_STRING = 6;
    const IS_RESOURCE = 7;

    public $value;
    public $type;
    public $refcount = 1;
    public $isRef = false;

    protected $dtorFunc;

    public function __construct($dtorFunc = null) {
        $this->dtorFunc = $dtorFunc;
    }

    public function __clone() {
        $this->refcount = 1;
        $this->isRef = false;
    }

    public static function factory($value = null) {
        $zval = new static;
        $zval->value = $value;
        $zval->rebuildType();
        return $zval;
    }

    public function copy(Zval $new) {
        $new->value = $this->value;
        $new->type = $this->type;
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

    public function getIterator() {
        if ($this->type == self::IS_ARRAY) {
            return new \ArrayIterator($this->value);
        }
        return new \EmptyIterator();
    }

    public function makePrintable() {
        switch ($this->type) {
            case static::IS_STRING:
                return $this;
            default:
                return static::factory($this->toString());
        }
    }

    public function rebuildType() {
        switch (gettype($this->value)) {
            case 'NULL':
                $this->type = static::IS_NULL;
                break;
            case 'integer':
                $this->type = static::IS_LONG;
                break;
            case 'boolean':
                $this->type = static::IS_BOOL;
                break;
            case 'double':
                $this->type = static::IS_DOUBLE;
                break;
            case 'string':
                $this->type = static::IS_STRING;
                break;
            case 'array':
                $this->type = static::IS_ARRAY;
                break;
            case 'object':
                $this->type = static::IS_OBJECT;
                break;
            case 'resource':
                $this->type = static::IS_RESOURCE;
                break;
            default:
                throw new \RuntimeException('FUBAR!');
        }
    }

    public function &separate() {
        if ($this->refcount > 1) {
            $ret = clone $this;
            $this->delRef();
            return $ret;
        }
        return $this;
    }

    public function &separateIfNotRef() {
        if (!$this->isRef) {
            return $this->separate();
        }
        return $this;
    }

    public function &separateIfRef() {
        if ($this->isRef) {
            return $this->separate();
        }
        $this->addRef();
        return $this;
    }

    public function toString() {
        return (string) $this->value;
    }

}