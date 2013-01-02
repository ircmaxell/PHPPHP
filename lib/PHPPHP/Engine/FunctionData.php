<?php

namespace PHPPHP\Engine;

interface FunctionData {
    public function execute(Executor $executor, \CanisM\HashTable\HashTable $args, \PHPPHP\Engine\Zval\Ptr $return);
}