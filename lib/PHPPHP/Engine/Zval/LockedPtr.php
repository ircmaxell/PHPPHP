<?php

namespace PHPPHP\Engine\Zval;

use PHPPHP\Engine\Zval;

class LockedPtr extends Ptr {

    public function makeRef() {
    }

    public function assignZval(Zval $value) {
        throw new \BadMethodCallException("Can't assign a locked pointer");
    }

    public function forceValue(Zval $value) {
        throw new \BadMethodCallException("Can't force a locked pointer");
    }

    public function setValue($value) {
        throw new \BadMethodCallException("Can't set a locked pointer");
    }

    public function separate() {
        throw new \BadMethodCallException("Can't separate a locked pointer");
    }

    public function separateIfNotRef() {
        throw new \BadMethodCallException("Can't separateIfNotRef a locked pointer");
    }

    public function &separateIfRef() {
        throw new \BadMethodCallException("Can't separateIfRef a locked pointer");
    }
}